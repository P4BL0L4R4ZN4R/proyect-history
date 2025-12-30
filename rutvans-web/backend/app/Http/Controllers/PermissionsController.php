<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionsController extends Controller
{
    public function index()
    {
        $permissions = Permission::all(); // Obtener todos los permisos
        return view('roles-permissions.permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        Permission::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);
        return redirect()->route('permissions.index')->with('success', '¡Permiso creado exitosamente!');
    }

    public function update(Request $request, Permission $permission)
    {
        $permission->update(['name' => $request->name]);
        return redirect()->route('permissions.index')->with('success', '¡Permiso actualizado correctamente!');
    }

    public function destroy(Permission $permission)
    {
        try {
            // Verificar si el permiso está asignado a modelos
            $modelsWithPermission = DB::table('model_has_permissions')->where('permission_id', $permission->id)->count();

            if ($modelsWithPermission > 0) {
                return redirect()->route('permissions.index')->with('error', 'No se puede eliminar: el permiso está asignado a ' . $modelsWithPermission . ' modelo(s).');
            }

            $permissionName = $permission->name;
            $permission->delete();

            return redirect()->route('permissions.index')->with('success', '¡Permiso \"' . $permissionName . '\" eliminado correctamente!');
        } catch (\Exception $e) {
            return redirect()->route('permissions.index')->with('error', 'Error al eliminar el permiso.');
        }
    }
}
