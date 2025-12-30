<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles-permissions.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles-permissions.roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|min:3|max:50',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web', // Explicitly set guard_name to 'web'
        ]);
        return redirect()->route('roles.index')->with('success', '¡Rol creado exitosamente!');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:50|unique:roles,name,' . $role->id,
        ]);

        $role->update(['name' => $request->name]);
        return redirect()->route('roles.index')->with('success', '¡Rol actualizado correctamente!');
    }

    public function destroy(Role $role)
    {
        try {
            // Verificar si el rol tiene usuarios asignados
            $usersWithRole = DB::table('model_has_roles')->where('role_id', $role->id)->count();

            if ($usersWithRole > 0) {
                return redirect()->route('roles.index')->with('error', 'No se puede eliminar: el rol está asignado a ' . $usersWithRole . ' usuario(s).');
            }

            $roleName = $role->name;
            $role->delete();

            return redirect()->route('roles.index')->with('success', '¡Rol "' . $roleName . '" eliminado correctamente!');
        } catch (\Exception $e) {
            return redirect()->route('roles.index')->with('error', 'Error al eliminar el rol.');
        }
    }
}
