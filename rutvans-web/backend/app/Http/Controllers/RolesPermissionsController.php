<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('roles-permissions.roles-permissions.index', compact('roles', 'permissions'));
    }

    public function update(Request $request, $roleId)
    {
        // Obtener el rol correctamente
        $role = Role::findOrFail($roleId);

        Log::info("Actualizando permisos para el rol ID: " . $role->id);

        // Obtener los permisos enviados (puede ser un array vacío si no se seleccionó ninguno)
        $permissionIds = $request->input('permissions', []);
        
        if (empty($permissionIds)) {
            // Si no hay permisos seleccionados, quitar todos los permisos del rol
            Log::info("No se seleccionaron permisos, quitando todos los permisos del rol ID: " . $role->id);
            $role->syncPermissions([]);
        } else {
            // Si hay permisos seleccionados, validar y asignar
            $permissions = Permission::whereIn('id', $permissionIds)->get();
            Log::info("Permisos validados: ", $permissions->pluck('name')->toArray());
            $role->syncPermissions($permissions);
        }

        Log::info("Permisos actualizados correctamente para el rol ID: " . $role->id);

        return redirect()->route('roles-permissions.index')->with('success', '¡Permisos actualizados correctamente!');
    }
    public function edit(Role $role)
    {
        $assignedPermissions = $role->permissions->pluck('id')->toArray();

        return response()->json([
            'assigned_permissions' => $assignedPermissions,
            'permissions' => Permission::select('id', 'name')->get()
        ]);
    }
}
