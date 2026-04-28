<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Etiqueta - @isset($reparacion){{ $reparacion->codigo_reparacion }}@else Entrada @endisset</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="/assets/css/argon-dashboard.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            html, body { margin: 0 !important; padding: 0 !important; background: #fff !important; }
            .container { max-width: 100% !important; }
            .etiqueta-hoja { box-shadow: none !important; border: 2px solid #000 !important; }
            .etiqueta-hoja, .etiqueta-hoja * { color: #000 !important; }
            .etiqueta-hoja .text-muted { color: #333 !important; }
        }
        .etiqueta-hoja { max-width: 95%; width: 800px; margin: 0 auto; min-height: 85vh; }
        .etiqueta-hoja .card-body { font-size: 1.1rem; }
        .etiqueta-hoja table { font-size: 1.05rem; }
        .etiqueta-hoja table td { padding: 0.4rem 0.5rem !important; }
        .etiqueta-hoja h5 { font-size: 1.5rem; }
    </style>
    @stack('css')
</head>
<body class="bg-gray-100">
    <div class="no-print py-3 px-3 d-flex gap-2 flex-wrap align-items-center">
        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Volver</a>
        <button type="button" class="btn btn-sm btn-primary" onclick="window.print();"><i class="fas fa-print me-1"></i>Imprimir</button>
        @auth
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary ms-auto"><i class="fas fa-home me-1"></i>Inicio</a>
        @endauth
    </div>
    <div class="container py-3">
        @yield('content')
    </div>
    @stack('js')
</body>
</html>
