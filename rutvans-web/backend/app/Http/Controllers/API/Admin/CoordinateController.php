<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coordinate;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Models\Role;

class CoordinateController extends Controller
{
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

        // Obtener rol "coordinate"
        $coordinateRoleId = Role::where('name', 'coordinate')->value('id');

        $query = Coordinate::with(['user', 'site'])
            ->whereHas('user.roles', function ($q) use ($coordinateRoleId) {
                $q->where('roles.id', $coordinateRoleId);
            })
            ->whereHas('user.companies', function ($q) use ($companyId) {
                $q->where('companies.id', $companyId)
                  ->where('company_users.status', 'active');
            });

        if ($request->has('site_id')) {
            $query->bySite($request->site_id);
        }

        $coordinates = $query->get()->map(function ($coordinate) {
            $photoUrl = null;
            if ($coordinate->photo) {
                $photoUrl = filter_var($coordinate->photo, FILTER_VALIDATE_URL)
                    ? $coordinate->photo
                    : URL::to('storage/' . $coordinate->photo);
            }
            $coordinate->photo_url = $photoUrl;
            return $coordinate;
        });

        return response()->json($coordinates, 200, [], JSON_PRETTY_PRINT);
    }

    public function show($id)
    {
        $coordinate = Coordinate::with(['user', 'site'])->find($id);

        if (!$coordinate) {
            return response()->json(['message' => 'Coordinador no encontrado'], 404);
        }

        if (!$coordinate->user->hasRole('coordinate')) {
            return response()->json(['message' => 'Usuario no es coordinador'], 403);
        }

        $photoUrl = null;
        if ($coordinate->photo) {
            $photoUrl = filter_var($coordinate->photo, FILTER_VALIDATE_URL)
                ? $coordinate->photo
                : URL::to('storage/' . $coordinate->photo);
        }

        $coordinate->photo_url = $photoUrl;

        return response()->json($coordinate, 200, [], JSON_PRETTY_PRINT);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string|max:20',
            'employee_code' => 'required|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'site_id' => 'required|exists:sites,id',
        ]);

        $userAuth = $request->user();

        // Obtener compañía activa
        $company = $userAuth->companies()->wherePivot('status', 'active')->first();

        if (!$company) {
            return response()->json([
                'message' => 'El usuario autenticado no tiene compañía activa asignada.',
            ], 400);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('coordinates', 'public');
        }

        // Crear usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
        ]);

        // Asignar rol "coordinate"
        $user->assignRole('coordinate');

        // Asociar usuario con la compañía activa
        $user->companies()->attach($company->id, [
            'role' => 'coordinate',
            'status' => 'active',
        ]);

        // Crear coordinate
        Coordinate::create([
            'user_id' => $user->id,
            'employee_code' => $request->employee_code,
            'photo' => $photoPath,
            'site_id' => $request->site_id,
        ]);

        return response()->json([
            'message' => 'Creación exitosa',
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $coordinate = Coordinate::find($id);

        if (!$coordinate) {
            return response()->json(['message' => 'Coordinador no encontrado'], 404);
        }

        $user = $coordinate->user;

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|string|min:6',
            'phone_number' => 'sometimes|required|string|max:20',
            'employee_code' => 'sometimes|required|string|max:50',
            'photo' => 'nullable|image|max:2048',
            'site_id' => 'sometimes|required|exists:sites,id',
        ]);

        // Subir nueva foto y eliminar anterior si existe
        if ($request->hasFile('photo')) {
            if ($coordinate->photo) {
                Storage::disk('public')->delete($coordinate->photo);
            }
            $photoPath = $request->file('photo')->store('coordinates', 'public');
            $coordinate->photo = $photoPath;
        }

        $coordinate->employee_code = $request->input('employee_code', $coordinate->employee_code);
        $coordinate->site_id = $request->input('site_id', $coordinate->site_id);
        $coordinate->save();

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('phone_number')) {
            $user->phone_number = $request->phone_number;
        }
        $user->save();

        return response()->json([
            'message' => 'Actualización exitosa',
        ]);
    }

    public function destroy($id)
    {
        $coordinate = Coordinate::find($id);

        if (!$coordinate) {
            return response()->json(['message' => 'Coordinador no encontrado'], 404);
        }

        if ($coordinate->photo) {
            Storage::disk('public')->delete($coordinate->photo);
        }

        $coordinate->delete();

        return response()->json(['message' => 'Coordinador eliminado correctamente']);
    }
}
