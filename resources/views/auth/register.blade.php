<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crear cuenta | Transportes EJE del Golfo</title>
    <link rel="stylesheet" href="{{ asset('css/ejegolfo.css') }}">
</head>
<body class="auth-page">
    <main class="auth-card">
        <a href="{{ route('home') }}" class="auth-brand">
            <svg class="bus-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 5h26a5 5 0 0 1 5 5v25a4 4 0 0 1-4 4H10a4 4 0 0 1-4-4V10a5 5 0 0 1 5-5Z" />
                <path d="M7 25h34M12 10h24v11H12zM12 30h5m14 0h5" />
                <circle cx="15" cy="38" r="3" />
                <circle cx="33" cy="38" r="3" />
            </svg>
            <span>EJE del Golfo</span>
        </a>
        <p class="eyebrow">CREA TU CUENTA</p>
        <h1>Crea tu cuenta</h1>
        <p class="auth-description">Regístrate para tener tus viajes a la mano.</p>

        @if ($errors->any())
            <div class="auth-errors" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if(config('services.google.client_id') && config('services.google.client_secret'))
        <a href="{{ route('google.login') }}" class="btn-google">
            <svg width="20" height="20" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"></path>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"></path>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"></path>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"></path>
            </svg>
            Continuar con Google
        </a>
        @endif

        <div style="text-align: center; margin-bottom: 1.5rem; color: #999; font-size: 0.85rem; position: relative;">
            <hr style="border: none; border-top: 1px solid #eee;">
            <span style="position: absolute; top: -10px; left: 50%; transform: translateX(-50%); background: white; padding: 0 10px;">o usa tu correo</span>
        </div>

        <form class="auth-form" method="POST" action="{{ route('register') }}">
            @csrf
            <label for="name">Nombre completo
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Tu nombre" autocomplete="name" required autofocus>
            </label>
            <label for="email">Correo electrónico
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nombre@correo.com" autocomplete="email" required>
            </label>
            <label for="password">Contraseña
                <input id="password" type="password" name="password" placeholder="Crea una contraseña" autocomplete="new-password" required>
            </label>
            <label for="password_confirmation">Confirmar contraseña
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repite tu contraseña" autocomplete="new-password" required>
            </label>
            <button class="btn-primary" type="submit">Crear mi cuenta</button>
        </form>

        <p class="auth-switch">¿Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
    </main>
</body>
</html>
