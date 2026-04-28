@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detalle del Equipo'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">{{ $equipo->tipo }}</h2>
                    <p class="text-white text-sm opacity-8">{{ $equipo->marca }} {{ $equipo->modelo }}</p>
                </div>
                <div>
                    <a href="{{ route('equipos.historial', $equipo) }}" class="btn btn-light me-2">
                        <i class="fas fa-history me-2"></i>Ver Historial Completo
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <!-- Información del Equipo -->
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Datos del Equipo</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <p class="text-xs text-secondary mb-1">Código Único (UUID)</p>
                            <p class="text-xs font-weight-bold mb-0 font-monospace">{{ $equipo->codigo_unico }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-xs text-secondary mb-1">Tipo</p>
                            <p class="text-sm font-weight-bold mb-0">{{ $equipo->tipo }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-xs text-secondary mb-1">Marca y Modelo</p>
                            <p class="text-sm font-weight-bold mb-0">{{ $equipo->marca }} {{ $equipo->modelo }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-xs text-secondary mb-1">Número de Serie</p>
                            <p class="text-sm font-weight-bold mb-0">{{ $equipo->numero_serie ?? 'No proporcionado' }}</p>
                        </div>
                        <div class="mb-3">
                            <p class="text-xs text-secondary mb-1">Estado Actual</p>
                            <span class="badge bg-gradient-{{ $equipo->estado == 'listo' ? 'success' : 'info' }}">
                                {{ ucfirst($equipo->estado) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Información del Cliente -->
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Cliente Propietario</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-sm font-weight-bold mb-1">{{ $equipo->cliente->nombre }}</p>
                        <p class="text-xs text-secondary mb-1">
                            <i class="fas fa-phone me-1"></i>{{ $equipo->cliente->telefono }}
                        </p>
                        @if($equipo->cliente->email)
                            <p class="text-xs text-secondary mb-0">
                                <i class="fas fa-envelope me-1"></i>{{ $equipo->cliente->email }}
                            </p>
                        @endif
                        <a href="{{ route('clientes.show', $equipo->cliente) }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                            Ver Cliente
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <!-- Descripción del Problema -->
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Problema Reportado</h6>
                    </div>
                    <div class="card-body">
                        <p class="text-sm mb-0">{{ $equipo->descripcion_problema }}</p>
                    </div>
                </div>

                <!-- Reparaciones del Equipo -->
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Reparaciones Realizadas ({{ $equipo->reparaciones->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @forelse($equipo->reparaciones as $reparacion)
                            <div class="border-bottom pb-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $reparacion->codigo_reparacion }}</h6>
                                        <p class="text-xs text-secondary mb-1">
                                            <i class="fas fa-calendar me-1"></i>
                                            Ingreso: {{ $reparacion->fecha_ingreso->format('d/m/Y') }}
                                            @if($reparacion->fecha_finalizacion)
                                                | Finalizado: {{ $reparacion->fecha_finalizacion->format('d/m/Y') }}
                                            @endif
                                        </p>
                                        <p class="text-xs mb-1">
                                            <i class="fas fa-user me-1"></i>
                                            <strong>Técnico:</strong> {{ $reparacion->tecnico ? $reparacion->tecnico->firstname . ' ' . $reparacion->tecnico->lastname : 'No asignado' }}
                                        </p>
                                        <p class="text-xs mb-1">
                                            <i class="fas fa-user-check me-1"></i>
                                            <strong>Recibido por:</strong> {{ $reparacion->recepcionista ? $reparacion->recepcionista->firstname . ' ' . $reparacion->recepcionista->lastname : 'No registrado' }}
                                        </p>
                                        @php
                                            $badgeClass = match($reparacion->estado) {
                                                'Recibido' => 'bg-gradient-secondary',
                                                'En Diagnóstico' => 'bg-gradient-info',
                                                'Esperando Pieza' => 'bg-gradient-warning',
                                                'En Proceso' => 'bg-gradient-primary',
                                                'Finalizado' => 'bg-gradient-success',
                                                default => 'bg-gradient-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $reparacion->estado }}</span>
                                        <span class="badge bg-gradient-dark">${{ number_format($reparacion->total_estimado, 2) }}</span>
                                    </div>
                                    <div>
                                        <a href="{{ route('reparaciones.show', $reparacion) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Ver Detalle
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-secondary text-center py-3">Este equipo aún no tiene reparaciones registradas</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

