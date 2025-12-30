<?php

namespace App\Http\Controllers;

use App\Models\Coordinate;
use App\Models\User;
use App\Models\CompanyUser;
use App\Models\Site;
use App\Helpers\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CoordinateController extends Controller
{
    public function __construct()
    {
        // Solo admin puede gestionar coordinadores
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:admin');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:6|confirmed',
            'address'       => 'required|string|max:255',
            'phone_number'  => 'required|string|max:20',
            'site_id'       => 'required|exists:sites,id',
            'photo_path'    => 'nullable|image|max:2048',
        ]);
            Log::info('STORE coordinador: inicio método', ['request' => $request->all()]);
            Log::info('STORE coordinador: validación exitosa', ['request' => $request->all()]);

        // Verificar que el admin solo pueda asignar coordinadores a sitios de su empresa
        $userCompany = CompanyUser::where('user_id', $user->id)
            ->where('role', 'admin')
            ->first();
        
        if (!$userCompany) {
            return response()->json([
                'message' => 'No tienes permisos para crear coordinadores.'
            ], 403);
        }
        
        // Verificar que el sitio pertenece a la empresa del admin
        $site = Site::find($request->site_id);
        if (!$site || $site->company_id != $userCompany->company_id) {
            return response()->json([
                'message' => 'No tienes permisos para asignar coordinadores a este sitio.'
            ], 403);
        }

            Log::info('STORE coordinador: antes de transacción', ['request' => $request->all()]);
            try {
        DB::transaction(function () use ($request) {
            // Crear usuario
            $user = User::create([
                'name'              => $request->name,
                'email'             => $request->email,
                'address'           => $request->address,
                'phone_number'      => $request->phone_number,
                'password'          => Hash::make($request->password),
                'email_verified_at' => now(), // <-- Marca como verificado
            ]);

            // Asignar rol de coordinator
            $user->assignRole('coordinate');

            // Obtener último código para ese sitio
            $lastCoordinate = Coordinate::where('site_id', $request->site_id)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;
            if ($lastCoordinate && preg_match('/COORD-(\d{4})/', $lastCoordinate->employee_code, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }

            $employeeCode = 'COORD-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Crear coordinador
            $coordinate = new Coordinate();
            $coordinate->user_id = $user->id;
            $coordinate->employee_code = $employeeCode;
            $coordinate->site_id = $request->site_id;

                if ($request->hasFile('photo_path')) {
                    $file = $request->file('photo_path');
                $extension = $file->getClientOriginalExtension();

                $normalizeString = function ($string) {
                    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
                    return preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($string));
                };

                $nameSlug = $normalizeString($user->name);
                $folderPath = "coordinators/{$user->id}/{$nameSlug}";
                $filename = "coordinator_photo_path.{$extension}";

                // Usar StorageHelper para crear directorio y almacenar archivo
                StorageHelper::makeDirectory($folderPath);
                $path = StorageHelper::storeFileAs($file, $folderPath, $filename);
                
                // Guardar en ambas tablas
                    Log::info('Ruta foto coordinador STORE:', ['user' => $user->id, 'ruta' => $path]);
                $coordinate->photo_path = $path;
                    $user->photo_path = $path;
                $user->save();
                    Log::info('Foto guardada en user STORE:', ['user' => $user->id, 'ruta' => $user->photo_path]);
            }

            $coordinate->save();

            // Vincular usuario coordinador al sitio en tabla pivote site_users
            $coordinate->site->users()->syncWithoutDetaching([$user->id]);
        });
            } catch (\Exception $e) {
                Log::error('Error en STORE coordinador:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json(['message' => 'Error al crear coordinador', 'error' => $e->getMessage()], 500);
            }

        return response()->json(['message' => 'Coordinador creado correctamente.']);
    }

    public function update(Request $request, Coordinate $coordinate)
    {
        $user = Auth::user();
        $coordinate->load('user');

        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $coordinate->user->id,
            'password'     => 'nullable|string|min:6',
            'address'      => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'photo_path'   => 'nullable|image|max:2048',
        ]);
            Log::info('UPDATE coordinador: inicio método', ['request' => $request->all()]);
            Log::info('UPDATE coordinador: validación exitosa', ['request' => $request->all()]);

        // Verificar que el admin solo pueda editar coordinadores de sitios de su empresa
        $userCompany = CompanyUser::where('user_id', $user->id)
            ->where('role', 'admin')
            ->first();
        
        if (!$userCompany) {
            return redirect()->back()->with('error', 'No tienes permisos para editar coordinadores.');
        }
        
        // Verificar que el coordinador pertenece a un sitio de la empresa del admin
        if ($coordinate->site->company_id != $userCompany->company_id) {
            return redirect()->back()->with('error', 'No tienes permisos para editar este coordinador.');
        }

            Log::info('UPDATE coordinador: antes de transacción', ['request' => $request->all()]);
            try {
        DB::transaction(function () use ($request, $coordinate) {
            $coordinatorUser = $coordinate->user;

            $normalizeString = function ($string) {
                $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
                return preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($string));
            };

            $oldNameSlug = $normalizeString($coordinatorUser->name);
            $oldFolder = "coordinators/{$coordinatorUser->id}/{$oldNameSlug}";

            // Actualizar datos del usuario
            $coordinatorUser->name         = $request->name;
            $coordinatorUser->email        = $request->email;
            $coordinatorUser->address      = $request->address;
            $coordinatorUser->phone_number = $request->phone_number;

            if ($request->filled('password')) {
                $coordinatorUser->password = Hash::make($request->password);
            }
            $coordinatorUser->save();

            $newNameSlug = $normalizeString($coordinatorUser->name);
            $newFolder = "coordinators/{$coordinatorUser->id}/{$newNameSlug}";

            // Mover carpeta si cambió el nombre
            if ($oldNameSlug !== $newNameSlug) {
                // Actualizar ruta de la foto en la base de datos
                if ($coordinate->photo_path) {
                        $newPhoto_pathPath = str_replace($oldFolder, $newFolder, $coordinate->photo_path);
                        $coordinate->photo_path = $newPhoto_pathPath;
                        $coordinatorUser->photo_path = $newPhoto_pathPath;
                }
            }

            // Manejar nueva foto
                if ($request->hasFile('photo_path')) {
                    $file = $request->file('photo_path');
                    $extension = $file->getClientOriginalExtension();
                    $filename = "coordinator_photo_path.{$extension}";

                    // Usar StorageHelper para crear directorio
                    StorageHelper::makeDirectory($newFolder);

                    // Eliminar foto anterior si existe
                    if ($coordinate->photo_path) {
                        StorageHelper::deletePublicFile($coordinate->photo_path);
                    }

                    // Almacenar nueva foto
                    $path = StorageHelper::storeFileAs($file, $newFolder, $filename);
                    
                    // Guardar en ambas tablas
                    Log::info('Ruta foto coordinador UPDATE:', ['user' => $coordinatorUser->id, 'ruta' => $path]);
                    $coordinate->photo_path = $path;
                    $coordinatorUser->photo_path = $path;
                    Log::info('Foto guardada en user UPDATE:', ['user' => $coordinatorUser->id, 'ruta' => $coordinatorUser->photo_path]);
                }

            // Guardar cambios en el usuario (incluye profile_photo_path_path si se actualizó)
            $coordinatorUser->save();
            $coordinate->save();
        });
            } catch (\Exception $e) {
                Log::error('Error en UPDATE coordinador:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return redirect()->back()->with('error', 'Error al actualizar coordinador: ' . $e->getMessage());
            }

        return redirect()->back()->with('success', 'Coordinador actualizado exitosamente.');
    }
}
