<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Email - RUTVANS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            display: flex;
            width: 800px;
            height: auto;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
        }

        .login-left {
            background-color: #ff6600;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .login-left img {
            width: 320px;
        }

        .login-right {
            flex: 1;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-right h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        .login-right p {
            font-size: 14px;
            color: #555;
            margin-bottom: 30px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: 500;
            font-size: 14px;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #ff6600;
            font-size: 14px;
            text-decoration: none;
        }

        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .remember-me input {
            margin-right: 8px;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            font-weight: bold;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            background-color: #000;
            color: #fff;
            transition: background-color 0.3s ease;
        }

        .btn-login:hover {
            background-color: #333;
        }

        .register-text {
            text-align: center;
            font-size: 14px;
        }

        .register-text a {
            color: #ff6600;
            font-weight: 600;
            text-decoration: none;
        }
        
        /* Specific styles for the verification message and links */
        .verification-message {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .verification-status {
            font-size: 14px;
            color: #28a745; /* Green for success message */
            margin-bottom: 20px;
            font-weight: 600;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            flex-wrap: wrap; /* Allow items to wrap on smaller screens */
        }

        .button-group .form-resend {
            flex: 1; /* Allow the form to take available space */
            margin-right: 15px; /* Space between button and links */
        }

        .button-group .btn-login {
            width: auto; /* Allow button to size naturally */
            min-width: 200px; /* Ensure a decent width for the button */
        }

        .verification-links {
            display: flex;
            flex-direction: column; /* Stack links vertically */
            align-items: flex-end; /* Align links to the right */
            gap: 8px; /* Space between stacked links */
        }

        .verification-links a {
            color: #ff6600;
            font-size: 14px;
            text-decoration: none;
        }
        
        .verification-links a:hover {
            text-decoration: underline;
        }

        /* Style for the logout button within the verification links */
        .verification-links .logout-button {
            background: none;
            border: none;
            padding: 0;
            margin: 0;
            font-family: inherit;
            font-size: 14px;
            color: #ff6600;
            text-decoration: none;
            cursor: pointer;
            text-align: right; /* Align button text to the right */
        }

        .verification-links .logout-button:hover {
            text-decoration: underline;
        }


        @media (max-width: 768px) {
            .login-box {
                flex-direction: column;
                width: 90%;
            }

            .login-left, .login-right {
                width: 100%;
                padding: 20px;
            }

            .login-left {
                justify-content: center;
            }

            .button-group {
                flex-direction: column; /* Stack buttons and links vertically */
                align-items: center; /* Center items when stacked */
            }

            .button-group .form-resend {
                margin-right: 0;
                margin-bottom: 15px; /* Space below the button */
                width: 100%;
            }

            .button-group .btn-login {
                width: 100%; /* Full width for the button on small screens */
            }

            .verification-links {
                width: 100%;
                align-items: center; /* Center links when stacked */
            }
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-left">
            <img src="{{ asset('asset/Logo_RutVans_Login1.png') }}" alt="Logo RUTVANS">
        </div>
        <div class="login-right">
            <h2>Verificar Correo Electrónico</h2>
            
            <div class="verification-message">
                {{ __('Antes de continuar, ¿podrías verificar tu dirección de correo electrónico haciendo clic en el enlace que acabamos de enviarte? Si no recibiste el correo electrónico, con gusto te enviaremos otro.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="verification-status">
                    {{ __('Se ha enviado un nuevo enlace de verificación a la dirección de correo electrónico que proporcionaste en la configuración de tu perfil.') }}
                </div>
            @endif

            <div class="button-group">
                <form method="POST" action="{{ route('verification.send') }}" class="form-resend">
                    @csrf
                    <button type="submit" class="btn-login">
                        {{ __('Reenviar Email de Verificación') }}
                    </button>
                </form>

                <div class="verification-links">
                    {{-- <a href="{{ route('profile.show') }}">
                        {{ __('Editar Perfil') }}
                    </a> --}}  <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="logout-button">
                            {{ __('Cerrar Sesión') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
