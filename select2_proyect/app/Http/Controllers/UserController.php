<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $users= User::all();

    return view('usuarios.usuario',
    ['users'=> $users, ]

    );

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // Importa la clase Hash

public function store(Request $request)
{
    // Cifra la contraseña utilizando bcrypt
    $password = Hash::make($request->input('password'));

    // Crea un nuevo usuario y guarda los datos
    $user = new User;
    $user->name = $request->input('name');
    $user->email = $request->input('email');
    $user->password = $password; // Asigna el hash de la contraseña
    $user->save();

    // Redirige a la ruta deseada después de guardar
    return redirect()->route('usuario.index');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    public function update(Request $request, string $id)
    {
    
        // Encuentra al usuario por su ID
        $user = User::findOrFail($id);
    
        // Actualiza los campos del usuario
        $user->name = $request->input('name');
        $user->email = $request->input('email');
    
        // Verifica si se proporcionó una nueva contraseña
        if ($request->filled('password')) {
            // Genera un nuevo hash para la nueva contraseña
            $password = Hash::make($request->input('password'));
            $user->password = $password; // Asigna el hash de la nueva contraseña
        }
    
        // Guarda los cambios en el usuario
        $user->save();
    
        // Redirige a la ruta deseada después de guardar
        return redirect()->route('usuario.index');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = user::findOrFail($id);
        $user->delete();
        
        return redirect()->route('usuario.index')->with('success', 'Usuario eliminado con éxito.');

        
    }

    public function logout(Request $request)
    {

    }
}
