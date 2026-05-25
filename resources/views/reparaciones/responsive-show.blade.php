@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => $reparacion->codigo_reparacion])
    
    <div class="container-fluid py-4">
        
        <!-- Header -->
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-4 gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <a href="{{ route('reparaciones.index') }}" class="mobile-only text-white text-decoration-none">
                        <i class="fas fa-arrow-left fa-lg"></i>
                    </a>
                    <h2 class="text-white mb-0">{{ $reparacion->codigo_reparacion }}</h2>
                </div>
                <p class="text-white opacity-8 mb-0">
                    Cliente: <span class="fw-bold">{{ $reparacion->equipo->cliente->nombre ?? 'N/A' }}</span>
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @auth
                    @if(auth()->user()->hasRole('tecnico') || auth()->user()->hasRole('administrador'))
                        @if(!$reparacion->tecnico_id)
                            <form action="{{ route('reparaciones.tomar-trabajo', $reparacion->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-responsive">
                                    <i class="fas fa-hand-paper me-2 me-md-2"></i>
                                    <span class="d-none d-md-inline">Tomar Trabajo</span>
                                    <span class="d-md-none">Tomar</span>
                                </button>
                            </form>
                        @elseif($reparacion->tecnico_id == auth()->id() && !in_array($reparacion->estado, ['Finalizado', 'Sin Reparación', 'Entregado']))
                            <form action="{{ route('reparaciones.completar-trabajo', $reparacion->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-responsive" onclick="return confirm('¿Marcar este trabajo como completado? Se calculará la comisión automáticamente.');">
                                    <i class="fas fa-check-circle me-2 me-md-2"></i>
                                    <span class="d-none d-md-inline">Completar Trabajo</span>
                                    <span class="d-md-none">Completar</span>
                                </button>
                            </form>
                        @endif
                    @endif
                @endauth
                <a href="{{ route('reparaciones.etiqueta-entrada', $reparacion) }}" class="btn btn-light text-dark btn-responsive" title="Imprimir hoja de entrada para pegar al equipo">
                    <i class="fas fa-print me-2 me-md-2"></i>
                    <span class="d-none d-md-inline">Imprimir etiqueta</span>
                    <span class="d-md-none">Imprimir</span>
                </a>
                <a href="{{ route('reparaciones.whatsapp-entrada', $reparacion->id) }}" class="btn btn-success btn-responsive" title="Enviar detalles de entrada por WhatsApp al cliente (puede adjuntar la foto manualmente)" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp me-2 me-md-2"></i>
                    <span class="d-none d-md-inline">WhatsApp entrada</span>
                    <span class="d-md-none">WS entrada</span>
                </a>
                @if($reparacion->equipo->cliente && trim($reparacion->equipo->cliente->telefono ?? '') && $reparacion->estado !== 'Pendiente Revisión Admin')
                <a href="{{ route('reparaciones.whatsapp-estado', $reparacion->id) }}" class="btn btn-dark btn-responsive" title="Notificar estado actual por WhatsApp" target="_blank" rel="noopener">
                    <i class="fas fa-bell me-2 me-md-2"></i>
                    <span class="d-none d-md-inline">Avisar estado</span>
                    <span class="d-md-none">Avisar</span>
                </a>
                @endif
                <a href="{{ route('reparaciones.edit', $reparacion->id) }}" class="btn btn-primary btn-responsive">
                    <i class="fas fa-edit me-2 me-md-2"></i>
                    <span class="d-none d-md-inline">Editar</span>
                </a>
                <button type="button" class="btn btn-danger btn-responsive" onclick="if(confirm('¿Está seguro de eliminar esta reparación? Esta acción no se puede deshacer.')) document.getElementById('delete-form').submit();">
                    <i class="fas fa-trash me-2 me-md-2"></i>
                    <span class="d-none d-md-inline">Eliminar</span>
                </button>
                <form id="delete-form" action="{{ route('reparaciones.destroy', $reparacion->id) }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Main Layout: Mobile stacked, Desktop 2 columns -->
        <div class="row g-4">
            
            <!-- Left Column -->
            <div class="col-12 col-lg-8">
                
                <!-- Alerta de Garantía -->
                @if($reparacion->es_garantia)
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="h5 fw-bold mb-0">
                            <i class="fas fa-shield-alt me-2"></i>
                            Reparación en Garantía
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Período de Garantía</label>
                                <p class="mb-0">
                                    <strong>{{ $reparacion->periodo_garantia_dias ?? 'N/A' }} días</strong>
                                </p>
                            </div>
                            @if($reparacion->fecha_vencimiento_garantia)
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Vence el</label>
                                <p class="mb-0">
                                    <strong>{{ $reparacion->fecha_vencimiento_garantia->format('d/m/Y') }}</strong>
                                    @if($reparacion->fecha_vencimiento_garantia->isPast())
                                        <span class="badge bg-danger ms-2">Vencida</span>
                                    @elseif($reparacion->fecha_vencimiento_garantia->diffInDays(now()) <= 7)
                                        <span class="badge bg-warning text-dark ms-2">Por vencer</span>
                                    @endif
                                </p>
                            </div>
                            @endif
                            @if($reparacion->reparacionOriginal)
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Reparación Original</label>
                                <p class="mb-0">
                                    <a href="{{ route('reparaciones.show', $reparacion->reparacionOriginal->id) }}" class="text-decoration-none">
                                        <strong>{{ $reparacion->reparacionOriginal->codigo_reparacion }}</strong>
                                        <i class="fas fa-external-link-alt ms-1"></i>
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        Cliente: {{ $reparacion->reparacionOriginal->equipo->cliente->nombre ?? 'N/A' }} - 
                                        Finalizada: {{ $reparacion->reparacionOriginal->fecha_finalizacion?->format('d/m/Y') ?? 'N/A' }}
                                    </small>
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Estado Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h5 fw-bold mb-3">
                            <i class="fas fa-tasks text-primary me-2"></i>
                            Estado de Reparación
                        </h3>
                        @if($reparacion->estado === 'Pendiente Revisión Admin')
                        <div class="alert alert-dark">
                            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start gap-3">
                                <div>
                                    <strong>Revisión interna pendiente.</strong>
                                    <div class="small mt-1">
                                        Esta cotización todavía no se ha enviado al cliente. Revísala, edítala si hace falta y luego confírmala desde aquí.
                                    </div>
                                    @if($reparacion->cotizacionRevisadaPor)
                                    <div class="small mt-2 text-white-50">
                                        Última revisión: {{ $reparacion->cotizacionRevisadaPor->firstname }} {{ $reparacion->cotizacionRevisadaPor->lastname }}
                                    </div>
                                    @endif
                                </div>
                                @if(auth()->user()->hasRole('administrador'))
                                <form method="POST" action="{{ route('reparaciones.enviar-cotizacion', $reparacion->id) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="btn btn-warning mb-0">
                                        <i class="fas fa-paper-plane me-2"></i>Confirmar y enviar al cliente
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endif
                        <form method="POST" action="{{ route('reparaciones.update', $reparacion->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold">Cambiar Estado</label>
                                    <select name="estado" class="form-select" required>
                                        <option value="Recibido" {{ $reparacion->estado == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                                        <option value="En Diagnóstico" {{ $reparacion->estado == 'En Diagnóstico' ? 'selected' : '' }}>En Diagnóstico</option>
                                        <option value="Pendiente Revisión Admin" {{ $reparacion->estado == 'Pendiente Revisión Admin' ? 'selected' : '' }}>Pendiente Revisión Admin</option>
                                        <option value="Esperando Aprobación" {{ $reparacion->estado == 'Esperando Aprobación' ? 'selected' : '' }}>Esperando Aprobación</option>
                                        <option value="Aprobado" {{ $reparacion->estado == 'Aprobado' ? 'selected' : '' }}>Aprobado</option>
                                        <option value="Esperando Pieza" {{ $reparacion->estado == 'Esperando Pieza' ? 'selected' : '' }}>Esperando Pieza</option>
                                        <option value="En Proceso" {{ $reparacion->estado == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="Finalizado" {{ $reparacion->estado == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                        <option value="Sin Reparación" {{ $reparacion->estado == 'Sin Reparación' ? 'selected' : '' }}>Sin Reparación</option>
                                        <option value="Entregado" {{ $reparacion->estado == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                                        <option value="Cancelado" {{ $reparacion->estado == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label small fw-bold">Comentario (Opcional)</label>
                                    <input type="text" name="comentario" class="form-control" placeholder="Agregar comentario sobre el cambio de estado...">
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="notificar_cliente" id="notificar_cliente" value="1">
                                        <label class="form-check-label small" for="notificar_cliente">
                                            Notificar al cliente por WhatsApp al guardar este cambio
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 w-md-auto">
                                        <i class="fas fa-save me-2"></i>
                                        Actualizar Estado
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="mt-3 p-3 bg-light rounded">
                            <span class="badge 
                                @if($reparacion->estado == 'Recibido') bg-secondary
                                @elseif($reparacion->estado == 'En Diagnóstico') bg-warning
                                @elseif($reparacion->estado == 'Pendiente Revisión Admin') bg-dark
                                @elseif($reparacion->estado == 'Esperando Pieza') bg-orange
                                @elseif($reparacion->estado == 'En Proceso') bg-info
                                @elseif($reparacion->estado == 'Finalizado') bg-success
                                @elseif($reparacion->estado == 'Sin Reparación') bg-warning
                                @elseif($reparacion->estado == 'Entregado') bg-primary
                                @else bg-danger
                                @endif px-3 py-2">
                                Estado Actual: {{ $reparacion->estado }}
                            </span>
                        </div>
                        @if($reparacion->cotizacion_revisada_at)
                        <div class="small text-muted mt-3">
                            Cotización revisada por administración el {{ $reparacion->cotizacion_revisada_at->format('d/m/Y H:i') }}.
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Información del Equipo -->
                <div class="accordion responsive-accordion mb-4" id="accordionEquipo">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEquipo">
                                <i class="fas fa-laptop me-2 text-primary"></i>
                                Información del Equipo
                            </button>
                        </h2>
                        <div id="collapseEquipo" class="accordion-collapse collapse show" data-bs-parent="#accordionEquipo">
                            <div class="accordion-body">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <label>Tipo de Dispositivo</label>
                                        <p>{{ $reparacion->equipo->tipo ?? 'N/A' }}</p>
                                    </div>
                                    <div class="info-item">
                                        <label>Número de Serie</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="font-monospace mb-0" id="numero_serie_display">{{ $reparacion->equipo->numero_serie ?? 'N/A' }}</p>
                                            @if(auth()->user()->hasRole('administrador'))
                                                <button type="button" class="btn btn-sm btn-link p-0" onclick="editarCampo('numero_serie')" title="Editar">
                                                    <i class="fas fa-edit text-primary"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <label>Marca/Modelo</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <p class="mb-0" id="marca_modelo_display">{{ $reparacion->equipo->marca ?? 'N/A' }} {{ $reparacion->equipo->modelo ?? '' }}</p>
                                            @if(auth()->user()->hasRole('administrador'))
                                                <button type="button" class="btn btn-sm btn-link p-0" onclick="editarCampo('marca_modelo')" title="Editar">
                                                    <i class="fas fa-edit text-primary"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <label>Código Único</label>
                                        <p class="font-monospace">{{ $reparacion->equipo->codigo_unico ?? 'N/A' }}</p>
                                    </div>
                                    <div class="info-item" style="grid-column: 1 / -1;">
                                        <label>Problema Reportado</label>
                                        <p>{{ $reparacion->equipo->descripcion_problema ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                
                                <div class="border-top mt-3 pt-3">
                                    <div class="row">
                                        @if($reparacion->tecnico)
                                        <div class="col-sm-6 mb-2">
                                            <label>Técnico Asignado</label>
                                            <p class="fw-bold">
                                                <i class="fas fa-user-cog text-info me-1"></i>
                                                {{ $reparacion->tecnico->firstname }} {{ $reparacion->tecnico->lastname }}
                                                @if(!$reparacion->tecnico_id)
                                                    <span class="badge bg-warning text-dark ms-2">Disponible</span>
                                                @endif
                                            </p>
                                        </div>
                                        @else
                                        <div class="col-sm-6 mb-2">
                                            <label>Técnico Asignado</label>
                                            <p class="fw-bold text-muted">
                                                <i class="fas fa-user-cog text-muted me-1"></i>
                                                Sin asignar - Disponible
                                            </p>
                                        </div>
                                        @endif
                                        @if($reparacion->tecnicoCompleto && $reparacion->monto_comision)
                                        <div class="col-sm-6 mb-2">
                                            <label>Técnico que Completó</label>
                                            <p class="fw-bold text-success">
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                {{ $reparacion->tecnicoCompleto->firstname }} {{ $reparacion->tecnicoCompleto->lastname }}
                                            </p>
                                        </div>
                                        <div class="col-sm-6 mb-2">
                                            <label>Comisión</label>
                                            <p class="fw-bold text-success">
                                                <i class="fas fa-dollar-sign text-success me-1"></i>
                                                ${{ number_format($reparacion->monto_comision, 2) }}
                                                <small class="text-muted">({{ $reparacion->porcentaje_comision }}%)</small>
                                            </p>
                                        </div>
                                        @endif
                                        @if($reparacion->recepcionista)
                                        <div class="col-sm-6 mb-2">
                                            <label>Recibido Por</label>
                                            <p class="fw-bold">
                                                <i class="fas fa-user text-success me-1"></i>
                                                {{ $reparacion->recepcionista->firstname }} {{ $reparacion->recepcionista->lastname }}
                                            </p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Galería de Fotos -->
                @if($reparacion->equipo->fotos->count() > 0)
                <div class="accordion responsive-accordion mb-4" id="accordionFotos">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFotos">
                                <i class="fas fa-images me-2 text-info"></i>
                                Fotografías del Equipo
                                <span class="badge bg-primary ms-2">{{ $reparacion->equipo->fotos->count() }}</span>
                            </button>
                        </h2>
                        <div id="collapseFotos" class="accordion-collapse collapse" data-bs-parent="#accordionFotos">
                            <div class="accordion-body">
                                <div class="row g-3">
                                    @foreach($reparacion->equipo->fotos as $foto)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ Storage::url($foto->ruta) }}" target="_blank" class="d-block">
                                            <img src="{{ Storage::url($foto->ruta) }}" 
                                                 class="img-fluid rounded shadow-sm" 
                                                 alt="Foto del equipo"
                                                 style="height: 200px; width: 100%; object-fit: cover; cursor: pointer; transition: transform 0.2s ease;"
                                                 onmouseover="this.style.transform='scale(1.05)'"
                                                 onmouseout="this.style.transform='scale(1)'">
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Notas del Técnico -->
                <div class="accordion responsive-accordion mb-4" id="accordionNotas">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotas">
                                <i class="fas fa-comment-medical me-2 text-warning"></i>
                                Notas del Técnico
                                <span class="badge bg-primary ms-2">{{ $reparacion->notas->count() }}</span>
                            </button>
                        </h2>
                        <div id="collapseNotas" class="accordion-collapse collapse" data-bs-parent="#accordionNotas">
                            <div class="accordion-body">
                                @forelse($reparacion->notas as $nota)
                                    <div class="border-start border-primary border-3 ps-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small fw-bold text-primary">
                                                {{ $nota->created_at->format('d/m/Y h:i A') }}
                                            </span>
                                            <span class="small text-muted">
                                                Por: {{ $nota->usuario->firstname ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <p class="small mb-0">{{ $nota->nota }}</p>
                                    </div>
                                @empty
                                    <p class="text-center text-muted py-3">No hay notas registradas</p>
                                @endforelse
                                
                                <!-- Add Note Form -->
                                <form method="POST" action="{{ route('reparaciones.notas', $reparacion->id) }}" class="mt-3 pt-3 border-top">
                                    @csrf
                                    <textarea name="nota" class="form-control mb-2" rows="2" placeholder="Agregar nueva nota..." required></textarea>
                                    <button type="submit" class="btn btn-sm btn-primary w-100">
                                        <i class="fas fa-plus me-2"></i>Agregar Nota
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Piezas Utilizadas -->
                <div class="accordion responsive-accordion mb-4" id="accordionPiezas">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePiezas">
                                <i class="fas fa-tools me-2 text-success"></i>
                                Piezas Utilizadas
                                <span class="badge bg-success ms-2">{{ $reparacion->piezas->count() }}</span>
                            </button>
                        </h2>
                        <div id="collapsePiezas" class="accordion-collapse collapse" data-bs-parent="#accordionPiezas">
                            <div class="accordion-body p-0">
                                @if($reparacion->piezas->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th class="small">Pieza</th>
                                                    <th class="small text-center">Cant.</th>
                                                    <th class="small text-end">P. Unit.</th>
                                                    <th class="small text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($reparacion->piezas as $pieza)
                                                    <tr>
                                                        <td class="small">{{ $pieza->nombre }}</td>
                                                        <td class="small text-center">{{ $pieza->cantidad }}</td>
                                                        <td class="small text-end">${{ number_format($pieza->precio_unitario, 2) }}</td>
                                                        <td class="small text-end fw-bold">${{ number_format($pieza->precio_total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                                <tr class="border-top border-2">
                                                    <td colspan="3" class="text-end fw-bold">Subtotal Piezas:</td>
                                                    <td class="text-end fw-bold text-primary">${{ number_format($reparacion->piezas->sum('precio_total'), 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-center text-muted py-4">No hay piezas registradas</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Summary -->
            <div class="col-12 col-lg-4">
                
                <!-- Resumen de Costos -->
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-gradient-primary text-white">
                        <h3 class="h5 fw-bold mb-0">
                            <i class="fas fa-calculator me-2"></i>
                            Resumen de Costos
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="text-muted">Diagnóstico:</span>
                            <span class="fw-bold text-dark">${{ number_format($reparacion->costo_diagnostico ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="text-muted">Piezas:</span>
                            <span class="fw-bold text-dark">${{ number_format($reparacion->costo_piezas ?? 0, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="text-muted">Mano de Obra:</span>
                            <span class="fw-bold text-dark">${{ number_format($reparacion->costo_mano_obra ?? 0, 2) }}</span>
                        </div>
                        
                        @if($reparacion->descripcion_cotizacion)
                        <div class="mb-3 pb-2 border-bottom">
                            <span class="text-muted d-block mb-2">Descripción de la Cotización:</span>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0 small" style="white-space: pre-line;">{{ $reparacion->descripcion_cotizacion }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @php
                            $tienePrecioCotizado = $reparacion->precio_cotizado && $reparacion->precio_cotizado > 0;
                            $cotizacionAprobada = $reparacion->cliente_aprobado === true;
                            $totalEstimado = (float) ($reparacion->total_estimado ?? 0);
                            $subtotalCot = $tienePrecioCotizado ? (float) $reparacion->precio_cotizado : $totalEstimado;
                    $aplicarImpuestoCot = ($impuestosActivos ?? true) && ($reparacion->factura ? $reparacion->factura->aplicar_impuesto : ($reparacion->aplicar_impuesto_cotizacion ?? false));
                            $pctImp = isset($porcentajeImpuesto) ? (float) $porcentajeImpuesto : 0;
                            $impuestosCot = ($subtotalCot > 0 && $aplicarImpuestoCot) ? round($subtotalCot * ($pctImp / 100), 2) : 0;
                            $totalConImpuesto = $subtotalCot + $impuestosCot;
                        @endphp
                        
                        @if($tienePrecioCotizado)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted">{{ $reparacion->estado === 'Sin Reparación' ? 'Costo del servicio:' : 'Precio Cotizado (subtotal):' }}</span>
                                <span class="fw-bold">${{ number_format($subtotalCot, 2) }}@if(!$cotizacionAprobada) <small class="text-muted">(Pendiente)</small>@endif</span>
                            </div>
                            @if($aplicarImpuestoCot)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted">Impuesto ({{ number_format($pctImp, 0) }}%):</span>
                                <span class="fw-bold">${{ number_format($impuestosCot, 2) }}</span>
                            </div>
                            @endif
                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small">Total estimado (diagnóstico + piezas + mano de obra):</span>
                                    <span class="text-muted small">${{ number_format($totalEstimado, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-5">{{ $cotizacionAprobada ? 'Total Aprobado:' : ($reparacion->estado === 'Sin Reparación' ? 'Total a cobrar:' : 'Total a pagar (cotizado):') }}</span>
                                    <span class="h4 fw-bold {{ $cotizacionAprobada ? 'text-success' : 'text-primary' }} mb-0">${{ number_format($totalConImpuesto, 2) }}</span>
                                </div>
                            </div>
                        @else
                            {{-- Si no hay precio cotizado, mostrar solo el total estimado --}}
                            <div class="border-top pt-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-5">{{ $reparacion->estado === 'Sin Reparación' ? 'Costo del servicio:' : 'Total Estimado:' }}</span>
                                    <span class="h4 fw-bold text-primary mb-0">${{ number_format($totalEstimado, 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Factura -->
                @if($reparacion->factura)
                <div class="card mb-4">
                    <div class="card-header bg-gradient-success text-white">
                        <h3 class="h5 fw-bold mb-0">
                            <i class="fas fa-file-invoice me-2"></i>
                            Factura
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted">Número:</span>
                                <span class="fw-bold">{{ $reparacion->factura->numero_factura }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted">Subtotal:</span>
                                <span class="fw-bold">${{ number_format($reparacion->factura->subtotal, 2) }}</span>
                            </div>
                            @if($reparacion->factura->aplicar_impuesto)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted">Impuestos:</span>
                                <span class="fw-bold">${{ number_format($reparacion->factura->impuestos, 2) }}</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted">Fecha:</span>
                                <span class="fw-bold">{{ $reparacion->factura->fecha_emision->format('d/m/Y') }}</span>
                            </div>
                            <div class="border-top pt-2 mt-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold fs-6">Total:</span>
                                    <span class="h5 fw-bold text-success mb-0">${{ number_format($reparacion->factura->total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="{{ route('facturas.show', $reparacion->factura) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-2"></i>Ver Factura Completa
                            </a>
                            <a href="{{ route('facturas.pdf', $reparacion->factura) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="fas fa-download me-2"></i>Descargar PDF
                            </a>
                            @if($reparacion->factura->cliente && $reparacion->factura->cliente->telefono)
                            <a href="{{ route('facturas.whatsapp', $reparacion->factura) }}" class="btn btn-success btn-sm" target="_blank">
                                <i class="fab fa-whatsapp me-2"></i>Compartir por WhatsApp
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Fechas Importantes -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="h5 fw-bold mb-3">
                            <i class="fas fa-calendar text-danger me-2"></i>
                            Fechas
                        </h3>
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Fecha de Ingreso</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-sign-in-alt text-info me-1"></i>
                                {{ $reparacion->fecha_ingreso ? $reparacion->fecha_ingreso->format('d/m/Y') : 'N/A' }}
                            </p>
                        </div>
                        @if($reparacion->fecha_prometida)
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold mb-1">Fecha Prometida</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-handshake text-warning me-1"></i>
                                {{ $reparacion->fecha_prometida->format('d/m/Y') }}
                            </p>
                        </div>
                        @endif
                        @if($reparacion->fecha_finalizacion)
                        <div>
                            <label class="small text-muted text-uppercase fw-bold mb-1">Fecha de Finalización</label>
                            <p class="fw-bold mb-0">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                {{ $reparacion->fecha_finalizacion->format('d/m/Y') }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Historial de Estados -->
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-info text-white">
                        <h3 class="h5 fw-bold mb-0">
                            <i class="fas fa-history me-2"></i>
                            Historial de Estados
                        </h3>
                    </div>
                    <div class="card-body">
                        <div style="max-height: 500px; overflow-y: auto;">
                            @forelse($reparacion->historialEstados as $historial)
                                <div class="timeline-item mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; min-width: 36px; font-size: 0.75rem;">
                                                <i class="fas fa-circle fa-xs"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <span class="badge bg-info mb-1">{{ $historial->estado }}</span>
                                                <span class="text-muted small">{{ $historial->created_at->format('d/m/Y') }}</span>
                                            </div>
                                            <p class="small text-muted mb-1">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $historial->created_at->format('h:i A') }}
                                                @if($historial->usuario)
                                                    <span class="ms-2">
                                                        <i class="fas fa-user me-1"></i>
                                                        {{ $historial->usuario->firstname }} {{ $historial->usuario->lastname }}
                                                    </span>
                                                @endif
                                            </p>
                                            @if($historial->comentario)
                                                <div class="mt-2 pt-2 border-top">
                                                    <p class="small mb-0 text-dark">
                                                        <i class="fas fa-info-circle me-1 text-info"></i>
                                                        {{ $historial->comentario }}
                                                    </p>
                                                    @if(auth()->user()->hasRole('administrador'))
                                                        <div class="d-flex gap-2 mt-2">
                                                            <button type="button"
                                                                    class="btn btn-outline-primary btn-sm py-1 px-2"
                                                                    onclick="editarComentarioHistorial('{{ route('reparaciones.historial.update', [$reparacion->id, $historial->id]) }}', @js($historial->comentario))">
                                                                <i class="fas fa-pen me-1"></i>Editar
                                                            </button>
                                                            <form method="POST" action="{{ route('reparaciones.historial.destroy', [$reparacion->id, $historial->id]) }}" onsubmit="return confirm('¿Quitar este comentario del historial?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2">
                                                                    <i class="fas fa-trash me-1"></i>Quitar
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-history fa-2x mb-2 opacity-25"></i>
                                    <p class="small mb-0">Sin historial registrado</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('js')
    <script>
        function editarComentarioHistorial(actionUrl, comentarioActual) {
            const nuevoComentario = prompt('Editar comentario del historial:', comentarioActual ?? '');
            if (nuevoComentario === null) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = actionUrl;
            form.style.display = 'none';

            form.innerHTML = `
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="comentario" value="">
            `;

            form.querySelector('input[name="comentario"]').value = nuevoComentario;
            document.body.appendChild(form);
            form.submit();
        }

        function editarCampo(campo) {
            const equipoId = {{ $reparacion->equipo->id }};
            let valorActual, label, inputType = 'text';
            
            if (campo === 'numero_serie') {
                valorActual = document.getElementById('numero_serie_display').textContent.trim();
                label = 'Número de Serie';
            } else if (campo === 'marca_modelo') {
                valorActual = document.getElementById('marca_modelo_display').textContent.trim();
                label = 'Marca y Modelo';
            }
            
            if (valorActual === 'N/A') valorActual = '';
            
            const nuevoValor = prompt(`Editar ${label}:`, valorActual);
            
            if (nuevoValor !== null && nuevoValor !== valorActual) {
                fetch(`/equipos/${equipoId}/actualizar`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        campo: campo,
                        valor: nuevoValor
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (campo === 'numero_serie') {
                            document.getElementById('numero_serie_display').textContent = nuevoValor || 'N/A';
                        } else if (campo === 'marca_modelo') {
                            const partes = nuevoValor.split(' ');
                            document.getElementById('marca_modelo_display').textContent = nuevoValor || 'N/A';
                        }
                        alert('Campo actualizado exitosamente');
                    } else {
                        alert('Error al actualizar: ' + (data.message || 'Error desconocido'));
                    }
                })
                .catch(error => {
                    // Error silencioso - solo registrar en desarrollo
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.error('Error:', error);
                    }
                    alert('Error al actualizar el campo');
                });
            }
        }
    </script>
    @endpush
@endsection
