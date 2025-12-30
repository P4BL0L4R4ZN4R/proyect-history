<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | outcome such as failure due to an invalid password / reset token.
    |
    */

    'reset' => '✅ Tu contraseña ha sido restablecida exitosamente.',
    'sent' => '📧 Te hemos enviado un enlace para restablecer tu contraseña a tu email.',
    'throttled' => '⏰ Por favor, espera antes de intentar nuevamente.',
    'token' => '❌ El enlace de restablecimiento es inválido o ha expirado. <a href="' . url('/forgot-password') . '" class="text-primary">Solicita uno nuevo.</a>',
    'user' => '❌ No encontramos ningún usuario con ese email. ¿Quizás quieras <a href="' . url('/register') . '" class="text-primary">crear una cuenta</a>?',

];
