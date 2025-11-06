@extends('layouts.app')

@section('title', 'Usuarios y Roles - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Usuarios y Roles</h1>
    <div class="page-actions">
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nuevo Usuario
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Usuarios</h3>
    </div>
    
    <!-- Vista de tabla para desktop -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Roles</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @foreach($usuario->roles as $rol)
                                <span class="badge badge-primary">{{ $rol->nombre }}</span>
                            @endforeach
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-sm btn-icon" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-icon btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No hay usuarios registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Vista de cards para móvil -->
    <div class="table-responsive-mobile">
        @forelse($usuarios as $usuario)
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h4 style="font-weight: 600; margin: 0; color: var(--text-primary);">{{ $usuario->name }}</h4>
                        <span style="font-size: 0.875rem; color: var(--text-secondary); margin-top: 0.25rem; display: block;">{{ $usuario->email }}</span>
                    </div>
                </div>
                <div class="table-card-body">
                    <div class="table-card-row">
                        <span class="table-card-label">Roles:</span>
                        <div class="table-card-value" style="text-align: right; flex-wrap: wrap; display: flex; justify-content: flex-end; gap: 0.25rem;">
                            @foreach($usuario->roles as $rol)
                                <span class="badge badge-primary">{{ $rol->nombre }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="table-card-actions">
                    <a href="{{ route('usuarios.show', $usuario->id) }}" class="btn btn-sm" title="Ver">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-sm btn-primary" title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center" style="padding: 2rem;">
                <p>No hay usuarios registrados</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
