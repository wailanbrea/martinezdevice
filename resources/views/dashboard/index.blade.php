@extends('layouts.app')

@section('title', 'Dashboard - Martínez Service')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<!-- Stats Cards -->
<div class="stats-grid" data-intro="Aquí puedes ver un resumen de estadísticas importantes. Haz clic en cualquier tarjeta para ver más detalles filtrados." data-step="1">
    <a href="{{ route('equipos.index', ['estado' => 'reparacion']) }}" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">En Reparación</div>
                    <div class="stat-value">{{ $enReparacion }}</div>
                </div>
                <div class="stat-icon" style="background: rgba(94, 114, 228, 0.1); color: #5e72e4;">
                    <i class="bi bi-wrench-adjustable"></i>
                </div>
            </div>
        </div>
    </a>
    
    <a href="{{ route('equipos.index', ['estado' => 'entregado']) }}" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Entregados</div>
                    <div class="stat-value">{{ $entregados }}</div>
                </div>
                <div class="stat-icon" style="background: rgba(45, 206, 137, 0.1); color: #2dce89;">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
    </a>
    
    <a href="{{ route('equipos.index', ['estado' => 'garantia']) }}" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">En Garantía</div>
                    <div class="stat-value">{{ $enGarantia }}</div>
                </div>
                <div class="stat-icon" style="background: rgba(251, 99, 64, 0.1); color: #fb6340;">
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>
        </div>
    </a>
    
    <a href="{{ route('reparaciones.index', ['estado' => 'pendiente']) }}" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-header">
                <div>
                    <div class="stat-label">Pendientes</div>
                    <div class="stat-value">{{ $pendientes }}</div>
                </div>
                <div class="stat-icon" style="background: rgba(17, 205, 239, 0.1); color: #11cdef;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
        </div>
    </a>
</div>

<!-- Charts and Tables Row -->
<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;" class="dashboard-grid">
    <!-- Chart Card -->
    <div class="card" data-intro="Este gráfico muestra la distribución de equipos por estado de reparación." data-step="2">
        <div class="card-header">
            <h3 class="card-title">Reparaciones por Estado</h3>
        </div>
        <div style="padding: 1rem;">
            <canvas id="reparacionesChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Últimas Entradas</h3>
        </div>
        <div class="table-container" data-intro="Tabla de últimas entradas de equipos al sistema." data-step="3">
            <table class="table">
                <thead>
                    <tr>
                        <th>Equipo</th>
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimasEntradas as $equipo)
                        <tr>
                            <td>{{ $equipo->tipo }} {{ $equipo->marca }} {{ $equipo->modelo }}</td>
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
                                    $estadoLabels = [
                                        'recibido' => 'Recibido',
                                        'diagnostico' => 'Diagnóstico',
                                        'reparacion' => 'En Reparación',
                                        'listo' => 'Listo',
                                        'entregado' => 'Entregado',
                                        'garantia' => 'En Garantía',
                                    ];
                                    $estadoLabel = $estadoLabels[$estado] ?? ucfirst($estado);
                                @endphp
                                <span class="badge {{ $badge }}">{{ $estadoLabel }}</span>
                            </td>
                            <td>{{ $equipo->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No hay equipos registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Chart.js Configuration
    const ctx = document.getElementById('reparacionesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Recibido', 'Diagnóstico', 'Reparación', 'Listo', 'Entregado'],
                datasets: [{
                    data: [
                        {{ $estadosEquipos['recibido'] }},
                        {{ $estadosEquipos['diagnostico'] }},
                        {{ $estadosEquipos['reparacion'] }},
                        {{ $estadosEquipos['listo'] }},
                        {{ $estadosEquipos['entregado'] }}
                    ],
                    backgroundColor: [
                        '#11cdef',
                        '#5e72e4',
                        '#fb6340',
                        '#2dce89',
                        '#8392ab'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
