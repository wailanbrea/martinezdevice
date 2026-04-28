<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Código no encontrado - Martinez Devices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .consulta-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="consulta-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="text-warning mb-3">
                            <i class="fas fa-exclamation-triangle fa-4x"></i>
                        </div>
                        <h2 class="mb-2">Código no encontrado</h2>
                        <p class="text-muted">Martinez Devices</p>
                    </div>

                    <p class="text-center">
                        {{ $mensaje ?? 'No encontramos ningún equipo con el código ingresado.' }}
                    </p>
                    @if(!empty($codigo))
                        <p class="text-center small text-muted">Código consultado: <code>{{ $codigo }}</code></p>
                    @endif

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ $urlConsulta ?? url('/consulta') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i> Volver a consultar
                        </a>
                    </div>

                    <p class="text-center text-muted small mt-4 mb-0">
                        Verifique que el código sea correcto. Si el problema continúa, contacte con nosotros.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
