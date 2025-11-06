@extends('layouts.app')

@section('title', 'Detalles del Cliente - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Detalles del Cliente</h1>
    <div class="page-actions">
        <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Editar
        </a>
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Información del Cliente</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-item">
                <label class="detail-label">Nombre</label>
                <div class="detail-value">{{ $cliente->nombre }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Cédula/RNC</label>
                <div class="detail-value">{{ $cliente->cedula_rnc }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Teléfono</label>
                <div class="detail-value">{{ $cliente->telefono ?? 'N/A' }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Correo Electrónico</label>
                <div class="detail-value">{{ $cliente->correo ?? 'N/A' }}</div>
            </div>

            @if($cliente->direccion)
            <div class="detail-item" style="grid-column: 1 / -1;">
                <label class="detail-label">Dirección</label>
                <div class="detail-value">{{ $cliente->direccion }}</div>
            </div>
            @endif

            <div class="detail-item">
                <label class="detail-label">Total de Equipos</label>
                <div class="detail-value">
                    <span class="badge badge-primary">{{ $cliente->equipos->count() }}</span>
                </div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Fecha de Registro</label>
                <div class="detail-value">{{ $cliente->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div class="detail-item">
                <label class="detail-label">Última Actualización</label>
                <div class="detail-value">{{ $cliente->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="form-actions mt-4">
            <a href="{{ route('clientes.edit', $cliente->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Editar Cliente
            </a>
            <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar este cliente? Esta acción también eliminará todos sus equipos asociados.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Eliminar Cliente
                </button>
            </form>
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver a la Lista
            </a>
        </div>
    </div>
</div>

@if($cliente->equipos->count() > 0)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Equipos del Cliente ({{ $cliente->equipos->count() }})</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Marca</th>
                    <th>Modelo</th>
                    <th>Número de Serie</th>
                    <th>Estado</th>
                    <th>Fecha de Ingreso</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cliente->equipos as $equipo)
                    <tr>
                        <td>{{ $equipo->tipo }}</td>
                        <td>{{ $equipo->marca ?? 'N/A' }}</td>
                        <td>{{ $equipo->modelo ?? 'N/A' }}</td>
                        <td>{{ $equipo->numero_serie ?? 'N/A' }}</td>
                        <td>
                            @php
                                $estadoBadge = [
                                    'recibido' => 'badge-info',
                                    'diagnostico' => 'badge-warning',
                                    'reparacion' => 'badge-warning',
                                    'listo' => 'badge-success',
                                    'entregado' => 'badge-primary',
                                    'garantia' => 'badge-danger',
                                ];
                                $badgeClass = $estadoBadge[$equipo->estado] ?? 'badge-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {{ ucfirst(str_replace('_', ' ', $equipo->estado)) }}
                            </span>
                        </td>
                        <td>{{ $equipo->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('equipos.show', $equipo->id) }}" class="btn btn-sm btn-icon" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('equipos.edit', $equipo->id) }}" class="btn btn-sm btn-icon btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

