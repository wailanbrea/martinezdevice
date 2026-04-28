@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<style>
    .btn-cta {
        background: #ff7a18;
        border-color: #ff7a18;
        color: #fff;
        box-shadow: 0 10px 24px rgba(255, 122, 24, 0.24);
    }

    .btn-cta:hover,
    .btn-cta:focus {
        background: #e96808;
        border-color: #e96808;
        color: #fff;
    }
</style>
@endpush

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Detalle del Usuario'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">{{ $usuario->firstname }} {{ $usuario->lastname }}</h2>
                    <p class="text-white text-sm opacity-8">Información del usuario del sistema</p>
                </div>
                <div>
                    <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-cta">
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
                <!-- Información del Usuario -->
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Datos del Usuario</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Nombre de Usuario</p>
                                <p class="text-sm font-weight-bold mb-0">@{{ $usuario->username }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Email</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    <i class="fas fa-envelope me-1 text-primary"></i>{{ $usuario->email }}
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Nombre</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $usuario->firstname }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Apellido</p>
                                <p class="text-sm font-weight-bold mb-0">{{ $usuario->lastname ?? 'No proporcionado' }}</p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Roles Asignados</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @forelse($usuario->roles as $rol)
                                        <span class="badge badge-sm bg-gradient-{{ $rol->slug == 'administrador' ? 'danger' : ($rol->slug == 'tecnico' ? 'info' : 'secondary') }}">
                                            {{ $rol->nombre }}
                                        </span>
                                    @empty
                                        <span class="text-secondary text-sm">Sin roles asignados</span>
                                    @endforelse
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Registrado</p>
                                <p class="text-sm mb-0">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Reparaciones (si es técnico) -->
                @if($usuario->hasRole('tecnico'))
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Estadísticas de Reparaciones</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Reparaciones Asignadas</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    <span class="badge badge-sm bg-gradient-info">{{ $usuario->reparaciones->count() }}</span>
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <p class="text-xs text-secondary mb-1">Reparaciones Completadas</p>
                                <p class="text-sm font-weight-bold mb-0">
                                    <span class="badge badge-sm bg-gradient-success">{{ $usuario->reparacionesCompletadas->count() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Panel Lateral -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Acciones Rápidas</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-cta w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Editar Usuario
                        </a>
                        @if($usuario->id !== auth()->id())
                            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" 
                                  onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Eliminar Usuario
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline-secondary w-100" disabled title="No puedes eliminar tu propio usuario">
                                <i class="fas fa-trash me-2"></i>Eliminar Usuario
                            </button>
                        @endif
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Volver a Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection
