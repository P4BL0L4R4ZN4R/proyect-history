<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            // Mensajes personalizados
            'name.required' => '👤 Tu nombre es obligatorio.',
            'name.max' => '👤 El nombre no puede tener más de 255 caracteres.',
            
            'email.required' => '📧 El email es obligatorio.',
            'email.email' => '📧 Por favor, introduce un email válido (ejemplo: usuario@dominio.com).',
            'email.unique' => '📧 Ya existe una cuenta con este email. <a href="' . route('login') . '" class="text-primary">¿Quieres iniciar sesión?</a>',
            
            'password.required' => '🔒 La contraseña es obligatoria.',
            'password.min' => '🔒 La contraseña debe tener al menos 8 caracteres para mayor seguridad.',
            'password.confirmed' => '🔒 La confirmación de contraseña no coincide.',
            
            'terms.accepted' => '📋 Debes aceptar los términos y condiciones para continuar.',
        ], [
            // Nombres de atributos personalizados
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'terms' => 'términos y condiciones',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Asignar rol por defecto de "client" si no se especifica otro
        $user->assignRole('client');

        return $user;
    }
}
