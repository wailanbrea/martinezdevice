<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Abriendo WhatsApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 text-center">
                        <div class="spinner-border text-success mb-3" role="status" aria-hidden="true"></div>
                        <h1 class="h4 mb-3">
                            {{ !empty($openInSameTab) ? 'Abriendo WhatsApp' : 'Abriendo WhatsApp en una ventana nueva' }}
                        </h1>
                        <p class="text-muted mb-4">{{ $successMessage }}</p>
                        <p class="small text-muted mb-4">
                            @if(!empty($openInSameTab))
                                En computadora este flujo evita bloqueos del navegador abriendo WhatsApp en esta misma pestaña.
                            @else
                                Si tu navegador bloquea la ventana, usa el botón de abajo. Luego volverás automáticamente al sistema.
                            @endif
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ $urlWhatsApp }}" @if(empty($openInSameTab)) target="_blank" rel="noopener" @endif class="btn btn-success">
                                Abrir WhatsApp
                            </a>
                            <a href="{{ $returnUrl }}" class="btn btn-outline-secondary">
                                Volver al sistema
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const openInSameTab = @json(!empty($openInSameTab));
        const urlWhatsApp = @json($urlWhatsApp);
        const returnUrl = @json($returnUrl);

        if (openInSameTab) {
            window.location.replace(urlWhatsApp);
        } else {
            window.open(urlWhatsApp, '_blank', 'noopener');
            setTimeout(function () {
                window.location.href = returnUrl;
            }, 900);
        }
    </script>
</body>
</html>
