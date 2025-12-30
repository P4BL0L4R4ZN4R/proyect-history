<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cashier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class CashierController extends Controller
{
    /**
     * Listar cajeros filtrados por compañía activa y rol 'cashier'
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Obtener compañía activa del usuario
        $company = $user->companies()->wherePivot('status', 'active')->first();

        if (!$company) {
            return response()->json([
                'total' => 0,
                'data' => [],
                'message' => 'El usuario no tiene compañía activa asignada',
            ]);
        }

        $companyId = $company->id;

        $cashierRoleId = Role::where('name', 'cashier')->value('id');

        $users = User::whereHas('roles', function ($query) use ($cashierRoleId) {
                $query->where('roles.id', $cashierRoleId);
            })
            ->whereHas('companies', function ($query) use ($companyId) {
                $query->where('companies.id', $companyId)
                      ->where('company_users.status', 'active');
            })
             ->whereHas('cashier')
            ->with(['cashier', 'companies'])
            ->get();

        $result = $users->map(function ($user) {
            $cashier = $user->cashier;

            $photoPath = $cashier->photo ?? $user->profile_photo_path;
            $fotoUrl = null;
            if ($photoPath) {
                $fotoUrl = filter_var($photoPath, FILTER_VALIDATE_URL)
                    ? $photoPath
                    : URL::to('storage/' . $photoPath);
            }

            $companies = $user->companies->filter(function($company) {
                return $company->pivot->status === 'active';
            })->map(function($company) {
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'role' => $company->pivot->role,
                    'status' => $company->pivot->status,
                ];
            })->values();

            return [
                'cashier_id' => $cashier->id ?? null,
                'user_id' => $user->id,
                'nombre' => $user->name,
                'telefono' => $user->phone_number,
                'email_usuario' => $user->email,
                'employee_code' => $cashier->employee_code ?? null,
                'foto' => $fotoUrl,
                'companies' => $companies,
            ];
        });

        return response()->json([
            'total' => $result->count(),
            'data' => $result
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Crear nuevo cajero con foto subida, asignar rol y asociar compañía activa
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'employee_code' => 'required|string|max:50|unique:cashiers,employee_code',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'foto_cajero' => 'nullable|image|max:2048',
            'site_id' => 'required|exists:sites,id',
        ]);

        $photoPath = null;
        if ($request->hasFile('foto_cajero')) {
            $photoPath = $request->file('foto_cajero')->store('cashiers', 'public');
        }

        $authUser = $request->user();
        $company = $authUser->companies()->wherePivot('status', 'active')->first();

        if (!$company) {
            return response()->json(['message' => 'No se encontró compañía activa'], 400);
        }

        // Crear usuario
        $user = User::create([
            'name' => $validatedData['nombre'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone_number' => $validatedData['telefono'] ?? null,
        ]);

        // Asignar rol 'cashier'
        $user->assignRole('cashier');

        // Asociar usuario a compañía con rol y estado
        $user->companies()->attach($company->id, ['role' => 'cashier', 'status' => 'active']);

        // Crear cajero asociado
        $cashier = Cashier::create([
            'user_id' => $user->id,
            'employee_code' => $validatedData['employee_code'],
            'photo' => $photoPath,
            'site_id' => $validatedData['site_id'],
        ]);

        return response()->json([
            'message' => 'Cajero creado exitosamente',
            'cashier' => $cashier,
            'user' => $user,
        ], 201);
    }

    /**
     * Mostrar cajero
     */
    public function show(Cashier $cashier)
{
    $cashier->load(['user', 'site']); // carga ambas relaciones

    $fotoUrl = null;
    if ($cashier->photo) {
        $fotoUrl = filter_var($cashier->photo, FILTER_VALIDATE_URL)
            ? $cashier->photo
            : URL::to('storage/' . $cashier->photo);
    } elseif ($cashier->user && $cashier->user->profile_photo_path) {
        $fotoUrl = filter_var($cashier->user->profile_photo_path, FILTER_VALIDATE_URL)
            ? $cashier->user->profile_photo_path
            : URL::to('storage/' . $cashier->user->profile_photo_path);
    }

    return response()->json([
        'cashier_id' => $cashier->id,
        'nombre' => $cashier->user->name ?? null,
        'telefono' => $cashier->user->phone_number ?? null,
        'email_usuario' => $cashier->user->email ?? null,
        'employee_code' => $cashier->employee_code,
        'site_id' => $cashier->site_id,
        'site' => $cashier->site ? [
            'id' => $cashier->site->id,
            'name' => $cashier->site->name,
            'address' => $cashier->site->address,
            // puedes agregar más campos que quieras exponer
        ] : null,
        'foto' => $fotoUrl,
    ]);
}


    /**
     * Actualizar cajero
     */
    public function update(Request $request, Cashier $cashier)
    {
        $user = $cashier->user;

        $validatedData = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'employee_code' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('cashiers', 'employee_code')->ignore($cashier->id)
            ],
            'telefono' => 'nullable|string|max:20',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id ?? null)
            ],
            'foto_cajero' => 'nullable|image|max:2048',
            'site_id' => 'sometimes|required|exists:sites,id',
        ]);

        if ($request->hasFile('foto_cajero')) {
            if ($cashier->photo) {
                Storage::disk('public')->delete($cashier->photo);
            }
            $photoPath = $request->file('foto_cajero')->store('cashiers', 'public');
            $validatedData['foto_cajero'] = $photoPath;
        }

        $cashier->update([
            'employee_code' => $validatedData['employee_code'] ?? $cashier->employee_code,
            'photo' => $validatedData['foto_cajero'] ?? $cashier->photo,
            'site_id' => $validatedData['site_id'] ?? $cashier->site_id,
        ]);

        if ($user) {
            $dataUser = [];
            if (isset($validatedData['nombre'])) {
                $dataUser['name'] = $validatedData['nombre'];
            }
            if (isset($validatedData['telefono'])) {
                $dataUser['phone_number'] = $validatedData['telefono'];
            }
            if (isset($validatedData['email'])) {
                $dataUser['email'] = $validatedData['email'];
            }
            if (!empty($dataUser)) {
                $user->update($dataUser);
            }
        }

        $cashier->load('user');

        $fotoUrl = null;
        if ($cashier->photo) {
            $fotoUrl = filter_var($cashier->photo, FILTER_VALIDATE_URL)
                ? $cashier->photo
                : URL::to('storage/' . $cashier->photo);
        } elseif ($cashier->user && $cashier->user->profile_photo_path) {
            $fotoUrl = filter_var($cashier->user->profile_photo_path, FILTER_VALIDATE_URL)
                ? $cashier->user->profile_photo_path
                : URL::to('storage/' . $cashier->user->profile_photo_path);
        }

        return response()->json([
            'cashier_id' => $cashier->id,
            'nombre' => $cashier->user->name ?? null,
            'telefono' => $cashier->user->phone_number ?? null,
            'email_usuario' => $cashier->user->email ?? null,
            'employee_code' => $cashier->employee_code,
            'site_id' => $cashier->site_id,
            'foto' => $fotoUrl
        ]);
    }

    /**
     * Eliminar cajero
     */
    public function destroy(Cashier $cashier)
    {
        if ($cashier->photo) {
            Storage::disk('public')->delete($cashier->photo);
        }

        $cashier->delete();

        return response()->json(null, 204);
    }
}
