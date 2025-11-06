@extends('layouts.app')

@section('title', 'Entradas - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Entradas</h1>
    <div class="page-actions">
        <a href="{{ route('equipos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Entrada
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Filtros de Búsqueda -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-funnel"></i> Filtros de Búsqueda
        </h3>
        <button type="button" class="btn btn-sm btn-secondary" id="toggleFilters" onclick="toggleFilters()">
            <i class="bi bi-chevron-down" id="filterIcon"></i>
        </button>
    </div>
    <div class="card-body" id="filtersContainer" style="display: none;">
        <form method="GET" action="{{ route('equipos.index') }}" id="filterForm">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Búsqueda General</label>
                    <input type="text" name="busqueda" class="form-control" 
                           value="{{ request('busqueda') }}" 
                           placeholder="Marca, modelo, serie, código...">
                </div>

                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="recibido" {{ request('estado') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                        <option value="diagnostico" {{ request('estado') == 'diagnostico' ? 'selected' : '' }}>En Diagnóstico</option>
                        <option value="reparacion" {{ request('estado') == 'reparacion' ? 'selected' : '' }}>En Reparación</option>
                        <option value="listo" {{ request('estado') == 'listo' ? 'selected' : '' }}>Listo</option>
                        <option value="entregado" {{ request('estado') == 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="garantia" {{ request('estado') == 'garantia' ? 'selected' : '' }}>En Garantía</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Cliente</label>
                    <select name="cliente_id" class="form-control">
                        <option value="">Todos los clientes</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo de Equipo</label>
                    <select name="tipo" class="form-control">
                        <option value="">Todos los tipos</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo }}" {{ request('tipo') == $tipo ? 'selected' : '' }}>
                                {{ $tipo }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('equipos.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Limpiar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
            <h3 class="card-title">Lista de Entradas
            @if(request()->hasAny(['busqueda', 'estado', 'cliente_id', 'tipo', 'fecha_desde', 'fecha_hasta']))
                <span class="badge badge-info">({{ $equipos->count() }} resultado(s))</span>
            @else
                <span class="badge badge-info">({{ $equipos->count() }} total)</span>
            @endif
        </h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Marca/Modelo</th>
                    <th>Cliente</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipos as $equipo)
                    <tr>
                        <td>{{ $equipo->tipo }}</td>
                        <td>{{ $equipo->marca }} {{ $equipo->modelo }}</td>
                        <td>{{ $equipo->cliente ? $equipo->cliente->nombre : 'N/A' }}</td>
                        <td>
                            @php
                                $badges = [
                                    'recibido' => 'badge-info',
                                    'diagnostico' => 'badge-primary',
                                    'reparacion' => 'badge-warning',
                                    'listo' => 'badge-success',
                                    'entregado' => 'badge-secondary',
                                    'garantia' => 'badge-danger'
                                ];
                                $estado = $equipo->estado ?? 'recibido';
                                $badge = $badges[$estado] ?? 'badge-secondary';
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($estado) }}</span>
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
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            @if(request()->hasAny(['busqueda', 'estado', 'cliente_id', 'tipo', 'fecha_desde', 'fecha_hasta']))
                                No se encontraron entradas con los filtros aplicados
                            @else
                                    No hay entradas registradas
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleFilters() {
    const container = document.getElementById('filtersContainer');
    const icon = document.getElementById('filterIcon');
    
    if (container.style.display === 'none') {
        container.style.display = 'block';
        icon.className = 'bi bi-chevron-up';
    } else {
        container.style.display = 'none';
        icon.className = 'bi bi-chevron-down';
    }
}

// Mostrar filtros si hay parámetros activos
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('busqueda') || urlParams.has('estado') || 
                       urlParams.has('cliente_id') || urlParams.has('tipo') || 
                       urlParams.has('fecha_desde') || urlParams.has('fecha_hasta');
    
    if (hasFilters) {
        const container = document.getElementById('filtersContainer');
        const icon = document.getElementById('filterIcon');
        if (container && icon) {
            container.style.display = 'block';
            icon.className = 'bi bi-chevron-up';
        }
    }
});
</script>
@endpush
