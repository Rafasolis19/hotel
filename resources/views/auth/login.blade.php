<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar sesión | Hotel</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #1b263b, #415a77);
        }

        .login-container {
            width: 400px;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .hotel-title {
            text-align: center;
            margin-bottom: 10px;
            color: #1b263b;
            font-size: 30px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #415a77;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        .remember input {
            width: auto;
        }

        .button {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #1b263b;
            color: white;
            font-size: 17px;
            cursor: pointer;
        }

        .button:hover {
            background: #415a77;
        }

        .forgot {
            text-align: center;
            margin-top: 20px;
        }

        .forgot a {
            color: #415a77;
            text-decoration: none;
        }

        .forgot a:hover {
            text-decoration: underline;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>

    <div class="login-container">

        <h1 class="hotel-title">HOTEL</h1>

        <p class="subtitle">Iniciar sesión</p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">

            @csrf

            <div class="form-group">
                <label for="email">Correo electrónico</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <div class="remember">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                >

                <label for="remember">Recordarme</label>
            </div>

            <button type="submit" class="button">
                Iniciar sesión
            </button>

        </form>

        <div class="forgot">
            <a href="#">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

    </div>

</body>
</html>