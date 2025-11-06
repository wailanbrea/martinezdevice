@extends('layouts.app')

@section('title', 'Editar Cliente - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Editar Cliente</h1>
    <div class="page-actions">
        <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-secondary">
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
        <h3 class="card-title">Información del Cliente</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label class="form-label">Nombre *</label>
                <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cliente->nombre) }}" required>
                @error('nombre')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Cédula/RNC *</label>
                <input type="text" name="cedula_rnc" class="form-control" value="{{ old('cedula_rnc', $cliente->cedula_rnc) }}" required>
                @error('cedula_rnc')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Teléfono</label>
                <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $cliente->telefono) }}">
                @error('telefono')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Correo Electrónico</label>
                <input type="email" name="correo" class="form-control" value="{{ old('correo', $cliente->correo) }}">
                @error('correo')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Dirección</label>
                <textarea name="direccion" class="form-control" rows="3">{{ old('direccion', $cliente->direccion) }}</textarea>
                @error('direccion')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Actualizar Cliente
                </button>
                <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

