<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transportes EJE del Golfo</title>
    
    <!-- Cargar nuestro CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('css/ejegolfo.css') }}">
</head>
<body>

    <!-- Header -->
    <header>
        <a href="/" class="logo">
            🚌 EJE del Golfo
        </a>
        <nav>
            <a href="#">Inicio</a>
            <a href="#">Rutas y Horarios</a>
            <a href="#">Nosotros</a>
            <a href="#">Ayuda</a>
            <a href="#" class="btn-login">Iniciar Sesión</a>
        </nav>
    </header>

    <!-- Aquí se inyectará el contenido de cada página -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; {{ date('Y') }} Transportes EJE del Golfo. Todos los derechos reservados.</p>
    </footer>

</body>
</html>