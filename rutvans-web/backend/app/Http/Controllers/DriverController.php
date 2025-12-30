<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Driver;
use App\Models\SiteUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DriverController extends Controller
{
    public function index()
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

        // Filtrar conductores por los sitios a los que tiene acceso
        $drivers = Driver::with('user')
            ->whereIn('site_id', $userSites)
            ->get();

        return view('empleados.drivers.index', compact('drivers', 'sites'));
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

        DB::transaction(function () use ($request, &$user, &$driver) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole('driver');

            $site_id = null;
            if (Auth::user()->hasRole('admin')) {
                $site_id = $request->site_id;
            } else {
                $siteUser = SiteUser::where('user_id', Auth::id())
                    ->where('status', 'active')
                    ->first();
                $site_id = $siteUser?->site_id;
            }

            $driver = Driver::create([
                'user_id' => $user->id,
                'site_id' => $site_id,
            ]);

            if ($site_id) {
                SiteUser::create([
                    'user_id' => $user->id,
                    'site_id' => $site_id,
                    'role'    => 'driver',
                    'status'  => 'active',
                ]);
            }

            // Manejar la foto del conductor
            if ($request->hasFile('photo_file')) {
                $photoFile = $request->file('photo_file');
                $photoExtension = $photoFile->getClientOriginalExtension();
                $photoFolder = "drivers/{$user->id}/photo";
                $photoFilename = "driver_photo.{$photoExtension}";
                Storage::disk('public')->makeDirectory($photoFolder);
                $photoPath = $photoFile->storeAs($photoFolder, $photoFilename, 'public');

                $driver->documents()->create([
                    'type' => 'foto',
                    'photo_path' => $photoPath,
                ]);
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'message' => 'Chófer creado correctamente.',
                'driver' => $driver,
            ]);
        }

        return redirect()->route('drivers.index')->with('success', 'Chófer creado correctamente.');
    }

    public function update(Request $request, Driver $driver)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email,' . $driver->user->id,
                'license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'license_expiration' => 'nullable|date',
                'password' => 'nullable|string|min:6|confirmed',
                'photo_file' => 'nullable|image|max:2048',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        DB::transaction(function () use ($request, $driver) {
            $user = $driver->user;
            $user->name  = $request->name;
            $user->email = $request->email;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->save();

            // Actualizar documento de licencia
            if ($request->hasFile('license_file')) {
                $licenseFile = $request->file('license_file');
                $licenseExtension = $licenseFile->getClientOriginalExtension();
                $licenseFolder = "drivers/{$user->id}/license";
                $licenseFilename = "license.{$licenseExtension}";
                Storage::disk('public')->makeDirectory($licenseFolder);
                $licensePath = $licenseFile->storeAs($licenseFolder, $licenseFilename, 'public');
                // Buscar documento existente
                $licenseDoc = $driver->documents()->where('type', 'licencia')->first();
                if ($licenseDoc) {
                    if (Storage::disk('public')->exists($licenseDoc->photo_path)) {
                        Storage::disk('public')->delete($licenseDoc->photo_path);
                    }
                    $licenseDoc->update([
                        'photo_path' => $licensePath,
                        'expiration_date' => $request->license_expiration,
                        'active' => true,
                    ]);
                } else {
                    $driver->documents()->create([
                        'type' => 'licencia',
                        'photo_path' => $licensePath,
                        'expiration_date' => $request->license_expiration,
                        'active' => true,
                    ]);
                }
            } elseif ($request->filled('license_expiration')) {
                // Solo actualizar fecha de expiración si no se subió archivo
                $licenseDoc = $driver->documents()->where('type', 'licencia')->first();
                if ($licenseDoc) {
                    $licenseDoc->update([
                        'expiration_date' => $request->license_expiration,
                    ]);
                }
            }

            // Actualizar documento de foto
            if ($request->hasFile('photo_file')) {
                $photoFile = $request->file('photo_file');
                $photoExtension = $photoFile->getClientOriginalExtension();
                $photoFolder = "drivers/{$user->id}/photo";
                $photoFilename = "driver_photo.{$photoExtension}";
                Storage::disk('public')->makeDirectory($photoFolder);
                $photoPath = $photoFile->storeAs($photoFolder, $photoFilename, 'public');
                $photoDoc = $driver->documents()->where('type', 'foto')->first();
                if ($photoDoc) {
                    if (Storage::disk('public')->exists($photoDoc->photo_path)) {
                        Storage::disk('public')->delete($photoDoc->photo_path);
                    }
                    $photoDoc->update([
                        'photo_path' => $photoPath,
                        'active' => true,
                    ]);
                } else {
                    $driver->documents()->create([
                        'type' => 'foto',
                        'photo_path' => $photoPath,
                        'active' => true,
                    ]);
                }
                $user->update(['profile_photo_path' => $photoPath]);
            }
        });

        if ($request->ajax()) {
            return response()->json(['message' => 'Chófer actualizado correctamente']);
        }

        return redirect()->route('drivers.index')->with('success', 'Chófer actualizado correctamente.');
    }

    public function destroy($id)
    {
        $driver = Driver::findOrFail($id);
        $user = $driver->user;

        DB::transaction(function () use ($driver, $user) {
            // 🔹 Obtener la carpeta específica drivers/{user_id}/{name_slug}
            if ($driver->photo) {
                $nameSlug = preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', $user->name)));
                $folderPath = "drivers/{$user->id}/{$nameSlug}";

                // 🔹 Eliminar solo la carpeta del nombre
                if (Storage::disk('public')->exists($folderPath)) {
                    Storage::disk('public')->deleteDirectory($folderPath);
                    Log::info("Deleted user-specific folder: {$folderPath}");
                } else {
                    Log::warning("Folder not found: {$folderPath}");
                }
            }

            // 🔹 Verificar si drivers/{user_id} está vacío y eliminarla
            $userFolder = "drivers/{$user->id}";
            if (Storage::disk('public')->exists($userFolder) && count(Storage::disk('public')->allFiles($userFolder)) === 0) {
                Storage::disk('public')->deleteDirectory($userFolder);
                Log::info("Deleted empty user ID folder: {$userFolder}");
            }

            // 🔹 Eliminar registros de conductor y usuario
            $driver->delete();
            $user->delete();
        });

        return redirect()->route('drivers.index')->with('success', 'Chófer eliminado correctamente.');
    }
}
