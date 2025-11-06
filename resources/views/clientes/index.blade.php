@extends('layouts.app')

@section('title', 'Clientes - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Clientes</h1>
    <div class="page-actions">
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Agregar Cliente
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Clientes</h3>
    </div>
    
    <!-- Vista de tabla para desktop -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Cédula/RNC</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Equipos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->cedula_rnc }}</td>
                        <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                        <td>{{ $cliente->correo ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-primary">{{ $cliente->equipos_count }}</span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-sm btn-icon" title="Ver" style="min-width: 36px; min-height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-icon btn-primary" title="Editar" style="min-width: 36px; min-height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este cliente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Eliminar" style="min-width: 36px; min-height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No hay clientes registrados</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Vista de cards para móvil -->
    <div class="table-responsive-mobile">
        @forelse($clientes as $cliente)
            <div class="table-card">
                <div class="table-card-header">
                    <div>
                        <h4 style="font-weight: 600; margin: 0; color: var(--text-primary);">{{ $cliente->nombre }}</h4>
                        <span class="badge badge-primary" style="margin-top: 0.25rem;">{{ $cliente->equipos_count }} Equipos</span>
                    </div>
                </div>
                <div class="table-card-body">
                    <div class="table-card-row">
                        <span class="table-card-label">Cédula/RNC:</span>
                        <span class="table-card-value">{{ $cliente->cedula_rnc }}</span>
                    </div>
                    <div class="table-card-row">
                        <span class="table-card-label">Teléfono:</span>
                        <span class="table-card-value">{{ $cliente->telefono ?? 'N/A' }}</span>
                    </div>
                    <div class="table-card-row">
                        <span class="table-card-label">Correo:</span>
                        <span class="table-card-value">{{ $cliente->correo ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="table-card-actions">
                    <a href="{{ route('clientes.show', $cliente->id) }}" class="btn btn-sm" title="Ver" style="min-width: 36px; min-height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-sm btn-primary" title="Editar" style="min-width: 36px; min-height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este cliente?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" style="min-width: 36px; min-height: 36px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center" style="padding: 2rem;">
                <p>No hay clientes registrados</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
