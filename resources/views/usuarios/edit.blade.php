@extends('layouts.app')

@section('title', 'Editar Usuario - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Usuario</h1>
    <div class="page-actions">
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>¡Error!</strong> Por favor corrige los siguientes errores:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Usuario</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nombre *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Correo Electrónico *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Dejar vacío para mantener la actual">
                <small class="form-text">Mínimo 8 caracteres. Solo completa si deseas cambiar la contraseña.</small>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Roles *</label>
                <div class="checkbox-group">
                    @foreach($roles as $rol)
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="roles[]" 
                                value="{{ $rol->id }}" 
                                id="role_{{ $rol->id }}"
                                {{ in_array($rol->id, old('roles', $usuario->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="role_{{ $rol->id }}">
                                {{ $rol->nombre }}
                                @if($rol->descripcion)
                                    <small class="text-muted">({{ $rol->descripcion }})</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('roles')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Actualizar Usuario
                </button>
                <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
