@extends('layouts.app')

@section('title', 'Detalles del Usuario - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Detalles del Usuario</h1>
    <div class="page-actions">
        <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Usuario</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <label class="detail-label">Nombre</label>
                <div class="detail-value">{{ $usuario->name }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Correo Electrónico</label>
                <div class="detail-value">{{ $usuario->email }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Roles</label>
                <div class="detail-value">
                    @forelse($usuario->roles as $rol)
                        <span class="badge badge-primary">{{ $rol->nombre }}</span>
                    @empty
                        <span class="text-muted">Sin roles asignados</span>
                    @endforelse
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Fecha de Registro</label>
                <div class="detail-value">{{ $usuario->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Última Actualización</label>
                <div class="detail-value">{{ $usuario->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="form-actions mt-4">
            <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar Usuario
            </a>
            <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este usuario?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Eliminar Usuario
                </button>
            </form>
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a la Lista
            </a>
        </div>
    </div>
</div>
@endsection
