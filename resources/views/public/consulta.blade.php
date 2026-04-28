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
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="consulta-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="mb-2">Consulta de Estado</h2>
                        <p class="text-muted">Martinez Devices</p>
                    </div>

                    <!-- Información del Equipo -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-laptop me-2"></i>Información del Equipo</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Tipo:</strong> {{ $equipo->tipo }}@if($equipo->tipo_personalizado) - {{ $equipo->tipo_personalizado }}@endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Marca:</strong> {{ $equipo->marca }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Modelo:</strong> {{ $equipo->modelo }}
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Número de Serie:</strong> {{ $equipo->numero_serie ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hasta 2 fotos del equipo (tomadas al crear el trabajo) -->
                    @if($equipo->fotos->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Fotos de tu equipo</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($equipo->fotos->take(2) as $foto)
                                <div class="col-6">
                                    <a href="{{ Storage::url($foto->ruta) }}" target="_blank" rel="noopener" class="d-block">
                                        <img src="{{ Storage::url($foto->ruta) }}" class="img-fluid rounded border" alt="Foto del equipo al ingreso">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($reparacionActual)
                    @php
                        $cotizacionDisponiblePublicamente = $reparacionActual->estado !== 'Pendiente Revisión Admin';
                    @endphp
                    <!-- Estado de la Reparación -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Estado de la Reparación</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Código:</strong> {{ $reparacionActual->codigo_reparacion }}
                            </div>
                            <div class="mb-3">
                                <strong>Estado:</strong>
                                <span class="badge status-badge bg-{{ $reparacionActual->estado == 'Pendiente Revisión Admin' ? 'secondary' : ($reparacionActual->estado == 'Esperando Aprobación' ? 'warning' : ($reparacionActual->estado == 'Aprobado' ? 'success' : 'info')) }}">
                                    {{ $reparacionActual->estado }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Fecha de Ingreso:</strong> {{ $reparacionActual->fecha_ingreso->format('d/m/Y') }}
                            </div>
                            @if($reparacionActual->fecha_prometida)
                            <div class="mb-3">
                                <strong>Fecha Prometida:</strong> {{ $reparacionActual->fecha_prometida->format('d/m/Y') }}
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cotización: mostrar cuando hay precio O cuando está esperando aprobación (para que el cliente siempre vea los botones) -->
                    @if($reparacionActual->estado == 'Pendiente Revisión Admin')
                    <div class="alert alert-secondary mb-4">
                        <i class="fas fa-user-shield me-2"></i>
                        La cotización de este equipo sigue en revisión interna. Cuando el taller la confirme, aquí aparecerá la opción para aprobarla o rechazarla.
                    </div>
                    @elseif($cotizacionDisponiblePublicamente && (($reparacionActual->precio_cotizado && $reparacionActual->precio_cotizado > 0) || $reparacionActual->estado == 'Esperando Aprobación'))
                    <div class="card mb-4 {{ $reparacionActual->estado == 'Esperando Aprobación' ? 'border-warning' : ($reparacionActual->cliente_aprobado === true ? 'border-success' : ($reparacionActual->cliente_aprobado === false ? 'border-danger' : '')) }}">
                        <div class="card-header {{ $reparacionActual->estado == 'Esperando Aprobación' ? 'bg-warning text-dark' : ($reparacionActual->cliente_aprobado === true ? 'bg-success text-white' : ($reparacionActual->cliente_aprobado === false ? 'bg-danger text-white' : 'bg-info text-white')) }}">
                            <h5 class="mb-0">
                                <i class="fas fa-dollar-sign me-2"></i>
                                @if($reparacionActual->estado == 'Esperando Aprobación')
                                    Cotización Pendiente de Aprobación
                                @elseif($reparacionActual->cliente_aprobado === true)
                                    Cotización Aprobada
                                @elseif($reparacionActual->cliente_aprobado === false)
                                    Cotización Rechazada
                                @else
                                    Cotización
                                @endif
                            </h5>
                        </div>
                        <div class="card-body">
                            @if($reparacionActual->precio_cotizado && $reparacionActual->precio_cotizado > 0)
                            @php
                                $aplicarImpuestoCot = $reparacionActual->factura ? $reparacionActual->factura->aplicar_impuesto : ($reparacionActual->aplicar_impuesto_cotizacion ?? true);
                                $pctImpuesto = isset($porcentajeImpuesto) ? (float) $porcentajeImpuesto : 18.00;
                                $subtotalCot = (float) $reparacionActual->precio_cotizado;
                                $impuestosCot = $aplicarImpuestoCot ? round($subtotalCot * ($pctImpuesto / 100), 2) : 0;
                                $totalConImpuesto = $subtotalCot + $impuestosCot;
                            @endphp
                            <div class="text-center mb-4">
                                <p class="text-muted mb-1">Subtotal (reparación)</p>
                                <p class="mb-1">${{ number_format($subtotalCot, 2) }}</p>
                                @if($aplicarImpuestoCot)
                                <p class="text-muted mb-1 mt-2">Impuesto ({{ number_format($pctImpuesto, 0) }}%)</p>
                                <p class="mb-1">${{ number_format($impuestosCot, 2) }}</p>
                                @endif
                                <h2 class="text-primary mb-2 mt-3">${{ number_format($totalConImpuesto, 2) }}</h2>
                                <p class="text-muted mb-1"><strong>Total a pagar</strong></p>
                                @if($reparacionActual->fecha_cotizacion)
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Cotizada el {{ $reparacionActual->fecha_cotizacion->format('d/m/Y H:i') }}
                                    </small>
                                @endif
                            </div>

                            @if($reparacionActual->descripcion_cotizacion)
                            <div class="mb-4">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="fas fa-clipboard-list me-2"></i>
                                            Detalles de la Cotización
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div style="white-space: pre-line; line-height: 1.6;">{{ $reparacionActual->descripcion_cotizacion }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @else
                            <p class="text-muted mb-4">La cotización está en proceso. Si ya le indicaron el precio, el taller debe registrarlo en el sistema para que pueda aprobar. Mientras tanto puede usar los botones a continuación (si al aprobar aparece un error, contacte al taller para que registren el precio cotizado).</p>
                            @endif

                            {{-- Botones Aprobar/Rechazar: siempre que esté esperando aprobación y el cliente no haya respondido --}}
                            @if($reparacionActual->estado == 'Esperando Aprobación' && $reparacionActual->cliente_aprobado !== true && $reparacionActual->cliente_aprobado !== false)
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Atención:</strong> Por favor, revise la cotización y decida si desea proceder con la reparación.
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-success btn-lg" onclick="aprobarCotizacion()" id="btnAprobar">
                                    <i class="fas fa-check me-2"></i>Aprobar Cotización
                                </button>
                                <button class="btn btn-danger btn-lg" onclick="rechazarCotizacion()" id="btnRechazar">
                                    <i class="fas fa-times me-2"></i>Rechazar Cotización
                                </button>
                            </div>
                            @elseif($reparacionActual->cliente_aprobado === true)
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Cotización aprobada</strong>
                                @if($reparacionActual->fecha_aprobacion)
                                    el {{ $reparacionActual->fecha_aprobacion->format('d/m/Y H:i') }}
                                @endif
                                <br>
                                <small>El técnico procederá con la reparación.</small>
                            </div>
                            @elseif($reparacionActual->cliente_aprobado === false)
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-times-circle me-2"></i>
                                <strong>Cotización rechazada</strong>
                                <br>
                                <small>La reparación ha sido cancelada.</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Historial -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Historial</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach($reparacionActual->historialEstados as $estado)
                                <div class="d-flex mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $estado->estado }}</h6>
                                        <p class="text-muted mb-1">{{ $estado->comentario }}</p>
                                        <small class="text-muted">{{ $estado->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const codigo = '{{ $equipo->codigo_unico }}';
        
        function aprobarCotizacion() {
            const btnAprobar = document.getElementById('btnAprobar');
            const btnRechazar = document.getElementById('btnRechazar');
            
            if (!confirm('¿Está seguro de aprobar esta cotización? Una vez aprobada, el técnico procederá con la reparación.')) {
                return;
            }

            // Deshabilitar botones
            if (btnAprobar) {
                btnAprobar.disabled = true;
                btnAprobar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            }
            if (btnRechazar) {
                btnRechazar.disabled = true;
            }

            fetch(`/api/public/aprobar/${codigo}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    location.reload();
                } else {
                    alert('✗ ' + (data.message || 'Error al aprobar la cotización'));
                    // Rehabilitar botones
                    if (btnAprobar) {
                        btnAprobar.disabled = false;
                        btnAprobar.innerHTML = '<i class="fas fa-check me-2"></i>Aprobar Cotización';
                    }
                    if (btnRechazar) {
                        btnRechazar.disabled = false;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('✗ Error al aprobar la cotización. Por favor, intente nuevamente.');
                // Rehabilitar botones
                if (btnAprobar) {
                    btnAprobar.disabled = false;
                    btnAprobar.innerHTML = '<i class="fas fa-check me-2"></i>Aprobar Cotización';
                }
                if (btnRechazar) {
                    btnRechazar.disabled = false;
                }
            });
        }

        function rechazarCotizacion() {
            const btnAprobar = document.getElementById('btnAprobar');
            const btnRechazar = document.getElementById('btnRechazar');
            
            if (!confirm('¿Está seguro de rechazar esta cotización? La reparación será cancelada.')) {
                return;
            }

            const comentario = prompt('¿Desea agregar un comentario sobre el rechazo? (opcional)\n\nPuede dejar en blanco si no desea agregar comentario.');
            
            // Si el usuario cancela el prompt, no hacer nada
            if (comentario === null) {
                return;
            }

            // Deshabilitar botones
            if (btnAprobar) {
                btnAprobar.disabled = true;
            }
            if (btnRechazar) {
                btnRechazar.disabled = true;
                btnRechazar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Procesando...';
            }

            fetch(`/api/public/rechazar/${codigo}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ comentario: comentario || '' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✓ ' + data.message);
                    location.reload();
                } else {
                    alert('✗ ' + (data.message || 'Error al rechazar la cotización'));
                    // Rehabilitar botones
                    if (btnAprobar) {
                        btnAprobar.disabled = false;
                    }
                    if (btnRechazar) {
                        btnRechazar.disabled = false;
                        btnRechazar.innerHTML = '<i class="fas fa-times me-2"></i>Rechazar Cotización';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('✗ Error al rechazar la cotización. Por favor, intente nuevamente.');
                // Rehabilitar botones
                if (btnAprobar) {
                    btnAprobar.disabled = false;
                }
                if (btnRechazar) {
                    btnRechazar.disabled = false;
                    btnRechazar.innerHTML = '<i class="fas fa-times me-2"></i>Rechazar Cotización';
                }
            });
        }
    </script>
</body>
</html>
