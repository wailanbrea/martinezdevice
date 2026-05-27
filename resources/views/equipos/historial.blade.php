@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Historial del Equipo'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">Historial Completo del Equipo</h2>
                    <p class="text-white text-sm opacity-8">{{ $equipo->marca }} {{ $equipo->modelo }}</p>
                </div>
                <div>
                    <a href="{{ route('equipos.show', $equipo) }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Información Básica -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p class="text-xs text-secondary mb-1">Cliente</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $equipo->cliente->nombre }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-xs text-secondary mb-1">Tipo de Equipo</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $equipo->tipo }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-xs text-secondary mb-1">Número de Serie</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $equipo->numero_serie ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="text-xs text-secondary mb-1">Total Reparaciones</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $equipo->reparaciones->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Reparaciones -->
        @forelse($equipo->reparaciones as $reparacion)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center pb-0">
                        <div>
                            <h6 class="mb-0">{{ $reparacion->codigo_reparacion }}</h6>
                            <p class="text-xs text-secondary mb-0">
                                {{ $reparacion->fecha_ingreso->format('d/m/Y') }} - 
                                @if($reparacion->fecha_finalizacion)
                                    {{ $reparacion->fecha_finalizacion->format('d/m/Y') }}
                                @else
                                    En proceso
                                @endif
                            </p>
                        </div>
                        <div>
                            <x-badge-estado :estado="$reparacion->estado" size="sm" />
                            <span class="badge bg-gradient-dark">${{ number_format($reparacion->total_estimado, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="text-xs mb-1">
                                    <i class="fas fa-user-tie me-1 text-primary"></i>
                                    <strong>Técnico:</strong> {{ $reparacion->tecnico ? $reparacion->tecnico->firstname . ' ' . $reparacion->tecnico->lastname : 'No asignado' }}
                                </p>
                                <p class="text-xs mb-0">
                                    <i class="fas fa-user-check me-1 text-success"></i>
                                    <strong>Recibido por:</strong> {{ $reparacion->recepcionista ? $reparacion->recepcionista->firstname . ' ' . $reparacion->recepcionista->lastname : 'No registrado' }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                @if($reparacion->factura)
                                    <p class="text-xs mb-0">
                                        <i class="fas fa-file-invoice me-1 text-warning"></i>
                                        <strong>Factura:</strong> {{ $reparacion->factura->numero_factura }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <!-- Costos -->
                        @if($reparacion->piezas->count() > 0)
                        <h6 class="text-sm mb-2">Piezas Utilizadas:</h6>
                        <table class="table table-sm table-borderless">
                            <tbody>
                                @foreach($reparacion->piezas as $pieza)
                                <tr>
                                    <td class="text-xs">{{ $pieza->nombre }}</td>
                                    <td class="text-xs text-center">x{{ $pieza->cantidad }}</td>
                                    <td class="text-xs text-end">${{ number_format($pieza->precio_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        <!-- Timeline de Estados -->
                        @if($reparacion->historialEstados->count() > 0)
                        <h6 class="text-sm mt-3 mb-2">Historial de Estados:</h6>
                        <div class="timeline timeline-one-side">
                            @foreach($reparacion->historialEstados as $estado)
                            <div class="timeline-block mb-2">
                                <span class="timeline-step timeline-step-sm">
                                    <i class="ni ni-check-bold text-success text-xs"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-xs font-weight-bold mb-0">{{ $estado->estado }}</h6>
                                    <p class="text-secondary text-xs mt-1 mb-0">
                                        {{ $estado->created_at->format('d/m/Y H:i') }} - {{ $estado->usuario->firstname }}
                                    </p>
                                    @if($estado->comentario)
                                        <p class="text-xs mb-0">{{ $estado->comentario }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        <div class="text-end mt-3">
                            <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-sm btn-primary">
                                Ver Detalle Completo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-tools fa-3x text-secondary mb-3"></i>
                        <p class="text-sm text-secondary">Este equipo aún no tiene reparaciones registradas</p>
                    </div>
                </div>
            </div>
        </div>
        @endforelse

        @include('layouts.footers.auth.footer')
    </div>
@endsection

