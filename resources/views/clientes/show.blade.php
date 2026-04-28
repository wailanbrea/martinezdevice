@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detalle del Cliente'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">{{ $cliente->nombre }}</h2>
                    <p class="text-white text-sm opacity-8">Información del cliente</p>
                </div>
                <div>
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Editar
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <!-- Información del Cliente -->
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Datos del Cliente</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Nombre Completo</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $cliente->nombre }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Cédula o RNC</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $cliente->cedula_rnc ?? 'No proporcionado' }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Teléfono</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    <i class="fas fa-phone me-1 text-primary"></i>{{ $cliente->telefono }}
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Correo Electrónico</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    @if($cliente->email)
                                        <i class="fas fa-envelope me-1 text-primary"></i>{{ $cliente->email }}
                                    @else
                                        <span class="text-secondary">No proporcionado</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-12 mb-3">
                                <p class="text-xs text-secondary mb-1">Dirección</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    @if($cliente->direccion)
                                        <i class="fas fa-map-marker-alt me-1 text-primary"></i>{{ $cliente->direccion }}
                                    @else
                                        <span class="text-secondary">No proporcionado</span>
                                    @endif
                                </p>
                            </div>

                            <div class="col-md-6">
                                <p class="text-xs text-secondary mb-1">Registrado</p>
                                <p class="text-sm mb-0">{{ $cliente->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipos del Cliente -->
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Equipos Registrados</h6>
                    </div>
                    <div class="card-body">
                        @forelse($cliente->equipos as $equipo)
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3">
                                <div>
                                    <h6 class="mb-1">{{ $equipo->tipo }}</h6>
                                    <p class="text-sm mb-0">{{ $equipo->marca }} {{ $equipo->modelo }}</p>
                                    <p class="text-xs text-secondary mb-0">
                                        <span class="badge badge-sm bg-gradient-{{ $equipo->estado == 'listo' ? 'success' : 'info' }}">
                                            {{ ucfirst($equipo->estado) }}
                                        </span>
                                        - {{ $equipo->reparaciones->count() }} reparaciones
                                    </p>
                                </div>
                                <div>
                                    @if($equipo->reparaciones->isNotEmpty())
                                        <a href="{{ route('reparaciones.show', $equipo->reparaciones->first()) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            Ver Última Reparación
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-secondary text-center py-3">Este cliente aún no tiene equipos registrados</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Acciones Rápidas</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('reparaciones.create') }}?cliente_id={{ $cliente->id }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-plus me-2"></i>Nueva Reparación
                        </a>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Editar Cliente
                        </a>
                        <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" 
                              onsubmit="return confirm('¿Estás seguro de eliminar este cliente?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-2"></i>Eliminar Cliente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection
