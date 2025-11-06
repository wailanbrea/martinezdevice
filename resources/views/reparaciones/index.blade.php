@extends('layouts.app')

@section('title', 'Historial de Reparaciones - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Historial de Reparaciones</h1>
    <div class="page-actions">
        <a href="{{ route('reparaciones.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nueva Reparación
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
        <form method="GET" action="{{ route('reparaciones.index') }}" id="filterForm">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Búsqueda General</label>
                    <input type="text" name="busqueda" class="form-control" 
                           value="{{ request('busqueda') }}" 
                           placeholder="Diagnóstico, observaciones, equipo, cliente...">
                </div>

                <div class="form-group">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-control">
                        <option value="">Todos los estados</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                        <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Técnico</label>
                    <select name="tecnico_id" class="form-control">
                        <option value="">Todos los técnicos</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}" {{ request('tecnico_id') == $tecnico->id ? 'selected' : '' }}>
                                {{ $tecnico->name }}
                            </option>
                        @endforeach
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
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Costo Mínimo</label>
                    <input type="number" name="costo_min" class="form-control" 
                           value="{{ request('costo_min') }}" 
                           placeholder="0.00" step="0.01" min="0">
                </div>

                <div class="form-group">
                    <label class="form-label">Costo Máximo</label>
                    <input type="number" name="costo_max" class="form-control" 
                           value="{{ request('costo_max') }}" 
                           placeholder="0.00" step="0.01" min="0">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Buscar
                </button>
                <a href="{{ route('reparaciones.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Limpiar Filtros
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Reparaciones
            @if(request()->hasAny(['busqueda', 'estado', 'tecnico_id', 'cliente_id', 'fecha_desde', 'fecha_hasta', 'costo_min', 'costo_max']))
                <span class="badge badge-info">({{ $reparaciones->count() }} resultado(s))</span>
            @else
                <span class="badge badge-info">({{ $reparaciones->count() }} total)</span>
            @endif
        </h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Cliente</th>
                    <th>Técnico</th>
                    <th>Estado</th>
                    <th>Costo Total</th>
                    <th>Fecha Inicio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reparaciones as $reparacion)
                    <tr>
                        <td>{{ $reparacion->equipo ? ($reparacion->equipo->tipo . ' ' . $reparacion->equipo->marca) : 'N/A' }}</td>
                        <td>{{ $reparacion->equipo && $reparacion->equipo->cliente ? $reparacion->equipo->cliente->nombre : 'N/A' }}</td>
                        <td>{{ $reparacion->tecnico ? $reparacion->tecnico->name : 'N/A' }}</td>
                        <td>
                            @php
                                $badges = [
                                    'pendiente' => 'badge-warning',
                                    'en_proceso' => 'badge-primary',
                                    'completada' => 'badge-success',
                                    'cancelada' => 'badge-danger'
                                ];
                                $estado = $reparacion->estado ?? 'pendiente';
                                $badge = $badges[$estado] ?? 'badge-secondary';
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $estado)) }}</span>
                        </td>
                        <td>${{ number_format($reparacion->total ?? 0, 2) }}</td>
                        <td>{{ $reparacion->fecha_inicio ? \Carbon\Carbon::parse($reparacion->fecha_inicio)->format('d/m/Y') : 'N/A' }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('reparaciones.show', $reparacion->id) }}" class="btn btn-sm btn-icon" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('reparaciones.edit', $reparacion->id) }}" class="btn btn-sm btn-icon btn-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            @if(request()->hasAny(['busqueda', 'estado', 'tecnico_id', 'cliente_id', 'fecha_desde', 'fecha_hasta', 'costo_min', 'costo_max']))
                                No se encontraron reparaciones con los filtros aplicados
                            @else
                                No hay reparaciones registradas
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
                       urlParams.has('tecnico_id') || urlParams.has('cliente_id') || 
                       urlParams.has('fecha_desde') || urlParams.has('fecha_hasta') || 
                       urlParams.has('costo_min') || urlParams.has('costo_max');
    
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
