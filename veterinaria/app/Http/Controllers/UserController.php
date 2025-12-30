<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Hasroles;

class UserController extends Controller
{



    public function index()
    {



        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {



        $roles = Role::whereIn('name', ['seller', 'cajero'])->get();
        return view('users.create', compact('roles'));
    }



        public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Asignación automática del rol (seller o cajero) según tu lógica
        $roleToAssign = $this->determineUserRole(); // Implementa esta función según tus necesidades
        $user->assignRole($roleToAssign);

        return redirect()->route('users.index')
            ->with('success', 'Usuario creado exitosamente como '.ucfirst($roleToAssign));
    }

    // Método para determinar el rol (ejemplo básico)
    protected function determineUserRole()
    {
        // Aquí implementa tu lógica para determinar si es seller o cajero
        // Por ejemplo:
        return (rand(0,1) == 0) ? 'seller' : 'cajero'; // Solo como ejemplo, cambia esto
    }



    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            // No incluimos el campo role aquí
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
               ->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
               ->with('success', 'Usuario eliminado exitosamente');
    }
}
