<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Driver;

class DriverApiController extends Controller
{
    /**
     * Lista de conductores filtrados por compañía activa y rol 'driver',
     * y que tengan registro activo en tabla drivers.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $company = $user->companies()->wherePivot('status', 'active')->first();

        if (!$company) {
            return response()->json([
                'total' => 0,
                'data' => [],
                'message' => 'El usuario no tiene compañía activa asignada',
            ]);
        }

        $companyId = $company->id;
        $driverRoleId = Role::where('name', 'driver')->value('id');

        $users = User::whereHas('roles', fn($q) => $q->where('roles.id', $driverRoleId))
            ->whereHas('companies', fn($q) => $q->where('companies.id', $companyId)->where('company_users.status', 'active'))
            ->whereHas('driver') // <-- Solo usuarios con driver asociado
            ->with(['driver.site', 'companies'])
            ->get();

        $result = $users->map(function ($user) {
            $driver = $user->driver;

            $photoPath = $driver->photo ?? $user->profile_photo_path;
            $fotoUrl = $photoPath ? (filter_var($photoPath, FILTER_VALIDATE_URL) ? $photoPath : URL::to('storage/' . $photoPath)) : null;

            return [
                'driver_id'       => $driver->id ?? null,
                'user_id'         => $user->id,
                'nombre'          => $user->name,
                'telefono'        => $user->phone_number,
                'email'           => $user->email,
                'licencia'        => $driver->license ?? null,
                'foto_conductor'  => $fotoUrl,
                'site_id'         => $driver->site_id ?? null,
                'site'            => $driver && $driver->site ? [
                    'id'      => $driver->site->id,
                    'name'    => $driver->site->name,
                    'address' => $driver->site->address,
                ] : null,
            ];
        });

        return response()->json([
            'total' => $result->count(),
            'data'  => $result
        ]);
    }

    /**
     * Crear nuevo conductor
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre'          => 'required|string|max:255',
            'licencia'        => 'required|string|max:255|unique:drivers,license',
            'telefono'        => 'nullable|string|max:20',
            'email'           => 'required|string|email|max:255|unique:users,email',
            'password'        => 'required|string|min:8',
            'foto_conductor'  => 'nullable|image|max:2048',
            'site_id'         => 'required|exists:sites,id',
        ]);

        $photoPath = $request->hasFile('foto_conductor')
            ? $request->file('foto_conductor')->store('drivers', 'public')
            : null;

        $authUser = $request->user();
        $company  = $authUser->companies()->wherePivot('status', 'active')->first();

        if (!$company) {
            return response()->json(['message' => 'No se encontró compañía activa'], 400);
        }

        $user = User::create([
            'name'         => $validatedData['nombre'],
            'email'        => $validatedData['email'],
            'password'     => Hash::make($validatedData['password']),
            'phone_number' => $validatedData['telefono'] ?? null,
        ]);

        $user->assignRole('driver');
        $user->companies()->attach($company->id, ['role' => 'driver', 'status' => 'active']);

        $driver = Driver::create([
            'user_id' => $user->id,
            'license' => $validatedData['licencia'],
            'photo'   => $photoPath,
            'site_id' => $validatedData['site_id'],
        ]);

        return response()->json([
            'message'         => 'Conductor creado exitosamente',
            'driver_id'       => $driver->id,
            'user_id'         => $user->id,
            'nombre'          => $user->name,
            'telefono'        => $user->phone_number,
            'email'           => $user->email,
            'licencia'        => $driver->license,
            'foto_conductor'  => $photoPath ? URL::to('storage/' . $photoPath) : null,
            'site_id'         => $driver->site_id,
        ], 201);
    }

    /**
     * Mostrar conductor
     */
    public function show(Driver $driver)
    {
        $driver->load('user', 'site');

        $fotoUrl = null;
        if ($driver->photo) {
            $fotoUrl = filter_var($driver->photo, FILTER_VALIDATE_URL)
                ? $driver->photo
                : URL::to('storage/' . $driver->photo);
        } elseif ($driver->user && $driver->user->profile_photo_path) {
            $fotoUrl = filter_var($driver->user->profile_photo_path, FILTER_VALIDATE_URL)
                ? $driver->user->profile_photo_path
                : URL::to('storage/' . $driver->user->profile_photo_path);
        }

        return response()->json([
            'driver_id'       => $driver->id,
            'user_id'         => $driver->user->id ?? null,
            'nombre'          => $driver->user->name ?? null,
            'telefono'        => $driver->user->phone_number ?? null,
            'email'           => $driver->user->email ?? null,
            'licencia'        => $driver->license,
            'foto_conductor'  => $fotoUrl,
            'site_id'         => $driver->site_id,
            'site'            => $driver->site ? [
                'id'      => $driver->site->id,
                'name'    => $driver->site->name,
                'address' => $driver->site->address,
            ] : null,
        ]);
    }

    /**
     * Actualizar conductor
     */
    public function update(Request $request, Driver $driver)
    {
        $user = $driver->user;

        $validatedData = $request->validate([
            'nombre'          => 'sometimes|required|string|max:255',
            'licencia'        => ['sometimes','required','string','max:255', Rule::unique('drivers', 'license')->ignore($driver->id)],
            'telefono'        => 'nullable|string|max:20',
            'email'           => ['nullable','string','email','max:255', Rule::unique('users', 'email')->ignore($user->id ?? null)],
            'foto_conductor'  => 'nullable|image|max:2048',
            'site_id'         => 'sometimes|required|exists:sites,id',
        ]);

        if ($request->hasFile('foto_conductor')) {
            if ($driver->photo) {
                Storage::disk('public')->delete($driver->photo);
            }
            $photoPath = $request->file('foto_conductor')->store('drivers', 'public');
            $validatedData['foto_conductor'] = $photoPath;
        }

        $driver->update([
            'license' => $validatedData['licencia'] ?? $driver->license,
            'photo'   => $validatedData['foto_conductor'] ?? $driver->photo,
            'site_id' => $validatedData['site_id'] ?? $driver->site_id,
        ]);

        if ($user) {
            $dataUser = [];
            if (isset($validatedData['nombre']))   $dataUser['name'] = $validatedData['nombre'];
            if (isset($validatedData['telefono'])) $dataUser['phone_number'] = $validatedData['telefono'];
            if (isset($validatedData['email']))    $dataUser['email'] = $validatedData['email'];
            if (!empty($dataUser)) $user->update($dataUser);
        }

        $driver->load('user', 'site');

        $fotoUrl = null;
        if ($driver->photo) {
            $fotoUrl = filter_var($driver->photo, FILTER_VALIDATE_URL)
                ? $driver->photo
                : URL::to('storage/' . $driver->photo);
        } elseif ($driver->user && $driver->user->profile_photo_path) {
            $fotoUrl = filter_var($driver->user->profile_photo_path, FILTER_VALIDATE_URL)
                ? $driver->user->profile_photo_path
                : URL::to('storage/' . $driver->user->profile_photo_path);
        }

        return response()->json([
            'driver_id'       => $driver->id,
            'user_id'         => $driver->user->id ?? null,
            'nombre'          => $driver->user->name ?? null,
            'telefono'        => $driver->user->phone_number ?? null,
            'email'           => $driver->user->email ?? null,
            'licencia'        => $driver->license,
            'foto_conductor'  => $fotoUrl,
            'site_id'         => $driver->site_id,
            'site'            => $driver->site ? [
                'id'      => $driver->site->id,
                'name'    => $driver->site->name,
                'address' => $driver->site->address,
            ] : null,
        ]);
    }

    /**
     * Eliminar conductor
     */
    public function destroy(Driver $driver)
    {
        if ($driver->photo) {
            Storage::disk('public')->delete($driver->photo);
        }
        $driver->delete();
        return response()->json(null, 204);
    }
}
