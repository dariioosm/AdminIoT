<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('titulo', 'Simulador IoT')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                Simulador IoT
            </a>
            <a class="navbar-brand" href="{{ route('dispositivos.index') }}">Todos los dispositivos</a>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('error'))
    <div class="alert alert-warning" role="alert">
        {{ session('error') }}
    </div>
@endif
        @yield('contenido')

    </main>
</body>
</html>
