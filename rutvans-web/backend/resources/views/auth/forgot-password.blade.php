<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - RUTVANS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Mismos estilos del login */
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

        input[type="email"] {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
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
            margin-bottom: 20px;
        }

        .btn-login:hover {
            background-color: #333;
        }

        .back-login {
            text-align: center;
            font-size: 14px;
        }

        .back-login a {
            color: #ff6600;
            font-weight: 600;
            text-decoration: none;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
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
            <h2>¿Olvidaste tu contraseña?</h2>
            <p>Ingresa tu correo electrónico y te enviaremos un enlace para restablecerla.</p>

            <!-- Éxito -->
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Errores -->
            @if ($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email" :value="old('email')" required autofocus>

                <button type="submit" class="btn-login">Enviar enlace de restablecimiento</button>

                <div class="back-login">
                    <a href="{{ route('login') }}">Volver al inicio de sesión</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
