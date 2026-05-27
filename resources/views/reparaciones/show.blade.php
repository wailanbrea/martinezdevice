@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detalle de Reparación'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">Reparación {{ $reparacion->codigo_reparacion }}</h2>
                    <p class="text-white text-sm opacity-8">Detalles de la orden de trabajo</p>
                </div>
                <div>
                    <a href="{{ route('reparaciones.edit', $reparacion) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header Card con información principal -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Cliente</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->cliente->nombre }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Equipo</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->tipo }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Fecha Ingreso</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->fecha_ingreso->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Fecha Prometida</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->fecha_prometida?->format('d/m/Y') ?? 'No definida' }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Teléfono</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->cliente->telefono }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Modelo</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->marca }} {{ $reparacion->equipo->modelo }}</p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Técnico Asignado</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    {{ $reparacion->tecnico ? $reparacion->tecnico->firstname . ' ' . $reparacion->tecnico->lastname : 'No asignado' }}
                                </p>
                            </div>
                            <div class="col-md-3 mb-3">
                                <p class="text-xs text-secondary mb-1">Recibido Por</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    {{ $reparacion->recepcionista ? $reparacion->recepcionista->firstname . ' ' . $reparacion->recepcionista->lastname : 'No registrado' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Email</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->cliente->email ?? 'No proporcionado' }}</p>
                            </div>
                            <div class="col-12">
                                <p class="text-xs text-secondary mb-1">Número de Serie</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->numero_serie ?? 'No proporcionado' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Columna Izquierda: Notas del Técnico -->
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6>Actualizaciones y Notas del Técnico</h6>
                    </div>
                    <div class="card-body">
                        <!-- Formulario para agregar nota -->
                        <form action="{{ route('reparaciones.notas', $reparacion) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <textarea name="nota" class="form-control" rows="3" 
                                              placeholder="Escribe una nueva nota o actualización..." required></textarea>
                                </div>
                                <div class="col-12 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Añadir Nota
                                    </button>
                                </div>
                            </div>
                        </form>

                        <hr>

                        <!-- Timeline de Notas -->
                        <div class="timeline timeline-one-side">
                            @forelse($reparacion->notas as $nota)
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="ni ni-single-02 text-primary"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">
                                        {{ $nota->usuario->firstname }} {{ $nota->usuario->lastname }}
                                    </h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        {{ $nota->created_at->diffForHumans() }}
                                    </p>
                                    <p class="text-sm mt-2 mb-0">
                                        {{ $nota->nota }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <p class="text-sm text-secondary">No hay notas registradas</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Detalles y Costos -->
            <div class="col-lg-4 mb-4">
                <!-- Estado Actual -->
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Detalles de la Reparación</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label text-xs">Estado actual</label>
                            <p>
                                <x-badge-estado :estado="$reparacion->estado" size="sm" />
                            </p>
                        </div>

                        <hr class="horizontal dark">

                        <!-- Piezas y Costos -->
                        <h6 class="mb-3">Piezas y Costos</h6>
                        
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th class="text-xs">Concepto</th>
                                    <th class="text-xs text-center">Cant.</th>
                                    <th class="text-xs text-end">Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-xs">Diagnóstico y Revisión</td>
                                    <td class="text-xs text-center">1</td>
                                    <td class="text-xs text-end">${{ number_format($reparacion->costo_diagnostico, 2) }}</td>
                                </tr>
                                @foreach($reparacion->piezas as $pieza)
                                <tr>
                                    <td class="text-xs">{{ $pieza->nombre }}</td>
                                    <td class="text-xs text-center">{{ $pieza->cantidad }}</td>
                                    <td class="text-xs text-end">${{ number_format($pieza->precio_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-top">
                                    <td class="text-sm font-weight-bold" colspan="2">Subtotal Piezas</td>
                                    <td class="text-sm font-weight-bold text-end">${{ number_format($reparacion->costo_piezas, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-sm" colspan="2">Mano de Obra</td>
                                    <td class="text-sm text-end">${{ number_format($reparacion->costo_mano_obra, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="text-base font-weight-bolder" colspan="2">Total Estimado</td>
                                    <td class="text-base font-weight-bolder text-end">${{ number_format($reparacion->total_estimado, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Estados -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Historial de Estados</h6>
                    </div>
                    <div class="card-body">
                        <div class="timeline timeline-one-side">
                            @foreach($reparacion->historialEstados as $estado)
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    @php
                                        $iconClass = match($estado->estado) {
                                            'Recibido' => 'ni-check-bold text-secondary',
                                            'En Diagnóstico' => 'ni-zoom-split-in text-info',
                                            'Esperando Pieza' => 'ni-time-alarm text-warning',
                                            'En Proceso' => 'ni-settings-gear-65 text-primary',
                                            'Finalizado' => 'ni-check-bold text-success',
                                            default => 'ni-check-bold text-secondary',
                                        };
                                    @endphp
                                    <i class="ni {{ $iconClass }}"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">{{ $estado->estado }}</h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        {{ $estado->created_at->format('d/m/Y - h:i A') }}
                                    </p>
                                    @if($estado->comentario)
                                    <p class="text-sm mt-2 mb-0">
                                        {{ $estado->comentario }}
                                    </p>
                                    @if(auth()->user()->hasRole('administrador'))
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm py-1 px-2"
                                                onclick="editarComentarioHistorial('{{ route('reparaciones.historial.update', [$reparacion->id, $estado->id]) }}', @js($estado->comentario))">
                                            <i class="fas fa-pen me-1"></i>Editar
                                        </button>
                                        <form method="POST" action="{{ route('reparaciones.historial.destroy', [$reparacion->id, $estado->id]) }}" onsubmit="return confirm('¿Quitar este comentario del historial?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm py-1 px-2">
                                                <i class="fas fa-trash me-1"></i>Quitar
                                            </button>
                                        </form>
                                    </div>
                                    @endif
                                    @endif
                                    <p class="text-xs text-muted mt-1">
                                        Por: {{ $estado->usuario->firstname }} {{ $estado->usuario->lastname }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

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
</script>
@endpush

