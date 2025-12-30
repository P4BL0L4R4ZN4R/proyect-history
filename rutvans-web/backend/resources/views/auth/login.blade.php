<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RUTVANS</title>
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
            margin-bottom: 20px; /* 👈 Solución clave */
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
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="login-left">
            <img src="{{ asset('asset/Logo_RutVans_Login1.png') }}" alt="Logo RUTVANS">
        </div>
        <div class="login-right">
            <h2>Bienvenido de nuevo</h2>
            <p>Por favor ingresa tu usuario y contraseña para continuar.</p>

            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div style="background-color: #fee; border: 1px solid #fcc; color: #c66; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    @foreach ($errors->all() as $error)
                        <div style="margin-bottom: 5px;">
                            {!! $error !!}
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Mostrar mensajes de sesión --}}
            @if (session('error'))
                <div style="background-color: #fee; border: 1px solid #fcc; color: #c66; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    {!! session('error') !!}
                </div>
            @endif

            @if (session('success'))
                <div style="background-color: #efe; border: 1px solid #cfc; color: #6c6; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    {!! session('success') !!}
                </div>
            @endif

            @if (session('status'))
                <div style="background-color: #eff; border: 1px solid #ccf; color: #66c; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div style="color: #c66; font-size: 12px; margin-top: 5px;">
                        {!! $message !!}
                    </div>
                @enderror

                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                @error('password')
                    <div style="color: #c66; font-size: 12px; margin-top: 5px;">
                        {!! $message !!}
                    </div>
                @enderror

                <div class="forgot-password">
                    <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
                </div>

                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recuérdame</label>
                </div>

                <button type="submit" class="btn-login">Entrar</button>

                <div class="register-text">
                    ¿No tienes cuenta?
                    <a href="{{ route('register') }}">Regístrate aquí</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
