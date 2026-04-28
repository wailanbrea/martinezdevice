<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Consulta de Estado - Martinez Devices</title>
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
                        <h2 class="mb-2">Consulta de Estado</h2>
                        <p class="text-muted">Martinez Devices</p>
                        <p class="small text-muted">Ingrese el código único de su equipo para ver el estado de su reparación.</p>
                    </div>

                    <form action="{{ url('/consulta') }}" method="get" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="codigo" class="form-label">Código del equipo</label>
                            <input type="text"
                                   class="form-control form-control-lg"
                                   id="codigo"
                                   name="codigo"
                                   placeholder="Ej: abc12345-6789-..."
                                   value="{{ old('codigo', request('codigo')) }}"
                                   required
                                   autofocus>
                            <div class="form-text">El código le fue entregado al registrar su equipo en reparación.</div>
                            <div class="invalid-feedback">Por favor ingrese su código.</div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search me-2"></i> Consultar estado
                            </button>
                        </div>
                    </form>

                    <p class="text-center text-muted small mt-4 mb-0">
                        ¿No tiene su código? Contacte con nosotros.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            'use strict';
            var form = document.querySelector('.needs-validation');
            if (form) {
                form.addEventListener('submit', function (event) {
                    var codigo = document.getElementById('codigo').value.trim();
                    if (!codigo) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            }
        })();
    </script>
</body>
</html>
