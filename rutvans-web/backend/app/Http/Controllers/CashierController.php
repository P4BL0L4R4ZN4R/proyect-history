<?php

namespace App\Http\Controllers;

use App\Models\Cashier;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CashierController extends Controller
{
    public function index(Request $request)
    {
        // Si el usuario es admin, obtiene los sitios de la compañía
        if (Auth::user()->hasRole('admin')) {
            // Obtener el company_id desde la relación companies (tabla pivote company_users)
            $companyId = Auth::user()->companies()->first()?->id;
            $sites = $companyId ? \App\Models\Site::where('company_id', $companyId)->get() : collect();
            $userSites = $sites->pluck('id');
        } else {
            $sites = Auth::user()->sites;
            $userSites = $sites->pluck('id');
        }

        // Filtrar cajeros por los sitios a los que tiene acceso
        $cashiers = Cashier::with(['user', 'site'])
            ->whereIn('site_id', $userSites)
            ->get();

        return view('empleados.cashiers.index', compact('cashiers', 'sites'));
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
                'photo_file' => 'nullable|image|max:2048',
            ];
            if (Auth::user()->hasRole('admin')) {
                $rules['site_id'] = 'required|exists:sites,id';
            }
            $validatedData = $request->validate($rules);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        try {
            DB::transaction(function () use ($request, &$user, &$cashier) {
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                $user->assignRole('cashier');

                $photoPath = null;
                if ($request->hasFile('photo_file') && $request->file('photo_file')->isValid()) {
                    $file = $request->file('photo_file');
                    $extension = $file->getClientOriginalExtension();

                    $normalizeString = fn($string) =>
                    strtolower(preg_replace('/[^a-zA-Z0-9_]/', '_', iconv('UTF-8', 'ASCII//TRANSLIT', $string)));

                    $nameSlug = $normalizeString($user->name);
                    $folderPath = "cashiers/{$user->id}/{$nameSlug}";
                    $filename = "cashier_photo.{$extension}";

                    Storage::disk('public')->makeDirectory($folderPath);
                    $photoPath = $file->storeAs($folderPath, $filename, 'public');
                    
                    // Actualizar también el photo_path del usuario
                    $user->update(['photo_path' => $photoPath]);
                }

                $site_id = null;
                if (Auth::user()->hasRole('admin')) {
                    $site_id = $request->site_id;
                } else {
                    $siteUser = SiteUser::where('user_id', Auth::id())
                        ->where('status', 'active')
                        ->first();
                    $site_id = $siteUser?->site_id;
                }

                $cashier = Cashier::create([
                    'user_id'       => $user->id,
                    'employee_code' => str_pad(
                        (Cashier::latest('id')->first()?->id ?? 0) + 1,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),
                    'photo_path'    => $photoPath,
                    'site_id'       => $site_id,
                ]);

                if ($site_id) {
                    SiteUser::create([
                        'user_id' => $user->id,
                        'site_id' => $site_id,
                        'role'    => 'cashier',
                        'status'  => 'active',
                    ]);
                }
            });
        } catch (\Exception $ex) {
            if ($request->ajax()) {
                return response()->json(['errors' => ['server' => [$ex->getMessage()]]], 500);
            }
            throw $ex;
        }

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Cajero creado correctamente.',
                'cashier' => $cashier,
            ]);
        }

        return redirect()->route('cashiers.index')->with('success', 'Cajero creado correctamente.');
    }

    public function update(Request $request, Cashier $cashier)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email,' . $cashier->user->id,
                'password' => 'nullable|string|min:6|confirmed',
                'photo_file' => 'nullable|image|max:2048',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        DB::transaction(function () use ($request, $cashier) {
            $user = $cashier->user;

            $normalizeString = function ($string) {
                $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
                $string = preg_replace('/[^a-zA-Z0-9_]/', '_', $string);
                return strtolower($string);
            };

            $oldNameSlug = $normalizeString($user->name);
            $oldFolder = "cashiers/{$user->id}/{$oldNameSlug}";

            // Actualizar datos del usuario
            $user->name  = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            $newNameSlug = $normalizeString($user->name);
            $newFolder = "cashiers/{$user->id}/{$newNameSlug}";

            // Si cambió el nombre y hay foto existente, mover archivos
            if ($oldNameSlug !== $newNameSlug && $cashier->photo_path && Storage::disk('public')->exists($cashier->photo_path)) {
                // Crear nueva carpeta
                Storage::disk('public')->makeDirectory($newFolder);
                
                // Obtener el nombre del archivo de la ruta actual
                $currentFileName = basename($cashier->photo_path);
                $newFilePath = "{$newFolder}/{$currentFileName}";
                
                // Mover el archivo
                if (Storage::disk('public')->move($cashier->photo_path, $newFilePath)) {
                    $cashier->photo_path = $newFilePath;
                }
            }

            // Guardar nueva foto si se subió
            if ($request->hasFile('photo_file')) {
                $extension = $request->file('photo_file')->getClientOriginalExtension();
                $filename = "cashier_photo.{$extension}";

                Storage::disk('public')->makeDirectory($newFolder);

                if ($cashier->photo_path && Storage::disk('public')->exists($cashier->photo_path)) {
                    Storage::disk('public')->delete($cashier->photo_path);
                }

                $path = $request->file('photo_file')->storeAs($newFolder, $filename, 'public');
                $cashier->photo_path = $path;
                
                // Actualizar también el photo_path del usuario
                $user->update(['photo_path' => $path]);
            }

            $cashier->save();
        });

        if ($request->ajax()) {
            return response()->json(['message' => 'Cajero actualizado correctamente']);
        }

        return redirect()->route('cashiers.index')->with('success', 'Cajero actualizado correctamente.');
    }

    public function destroy($id)
    {
        $cashier = Cashier::findOrFail($id);
        $user = $cashier->user;

        DB::transaction(function () use ($cashier, $user) {
            // 🔹 Obtener la carpeta específica coordinators/{user_id}/{name_slug}
            if ($cashier->photo) {
                $nameSlug = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $user->name)));
                $folderPath = "cashiers/{$user->id}/{$nameSlug}";

                // 🔹 Eliminar solo la carpeta del nombre
                if (Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                    Log::info("Deleted user-specific folder: {$folderPath}");
                } else {
                    Log::warning("Folder not found: {$folderPath}");
                }
            }

            // 🔹 Verificar si coordinators/{user_id} está vacío y eliminarla
            $userFolder = "cashiers/{$user->id}";
            if (Storage::disk('public')->exists($userFolder) && count(Storage::disk('public')->allFiles($userFolder)) === 0) {
                Storage::disk('public')->deleteDirectory($userFolder);
                Log::info("Deleted empty user ID folder: {$userFolder}");
            }

            // 🔹 Eliminar registros de coordinador y usuario
            $cashier->delete();
            $user->delete();
        });

        return redirect()->route('cashiers.index')->with('success', 'Cajero eliminado correctamente.');
    }
}
