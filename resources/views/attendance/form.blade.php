<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia al Evento</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5 p-4 bg-white shadow rounded">
        <div class="text-center mb-4">
            <h1 class="display-6">Registrar Asistencia</h1>
            <p class="text-muted fs-5">{{ $event->name }}</p>
        </div>

        <livewire:attendance-form :event="$event" />

    </div>

    <!-- Bootstrap JS y dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    @livewireScripts
</body>
</html>