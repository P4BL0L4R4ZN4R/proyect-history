<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'Las credenciales introducidas no coinciden con nuestros registros.',
    'password' => 'La contraseña es incorrecta.',
    'throttle' => 'Demasiados intentos de inicio de sesión. Inténtalo de nuevo en :seconds segundos.',

    // Mensajes personalizados para login
    'login' => [
        'failed' => '❌ Email o contraseña incorrectos. Por favor, verifica tus datos.',
        'blocked' => '🚫 Tu cuenta ha sido bloqueada. Contacta al administrador.',
        'unverified' => '📧 Debes verificar tu email antes de iniciar sesión.',
        'throttle' => '⏰ Demasiados intentos fallidos. Espera :seconds segundos antes de intentar nuevamente.',
    ],

    // Mensajes para registro
    'register' => [
        'success' => '✅ ¡Cuenta creada exitosamente! Te hemos enviado un email de verificación.',
        'failed' => '❌ No se pudo crear la cuenta. Inténtalo nuevamente.',
        'email_exists' => '📧 Ya existe una cuenta con este email. ¿Quieres iniciar sesión?',
    ],

    // Mensajes de verificación de email
    'verification' => [
        'sent' => '📧 Te hemos enviado un nuevo enlace de verificación a tu email.',
        'verified' => '✅ ¡Email verificado exitosamente! Ahora puedes iniciar sesión.',
        'invalid' => '❌ El enlace de verificación es inválido o ha expirado.',
    ],

    // Mensajes de recuperación de contraseña
    'passwords' => [
        'sent' => '📧 Te hemos enviado un enlace para restablecer tu contraseña.',
        'reset' => '✅ Tu contraseña ha sido restablecida exitosamente.',
        'token' => '❌ El enlace de restablecimiento es inválido o ha expirado.',
        'user' => '❌ No encontramos ningún usuario con ese email.',
        'throttled' => '⏰ Espera antes de intentar nuevamente.',
    ],

];
