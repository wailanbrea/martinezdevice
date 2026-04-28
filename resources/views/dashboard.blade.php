@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<style>
    .chart {
        width: 100% !important;
        overflow: hidden;
    }
    #chart-bars, #chart-pie {
        width: 100% !important;
        height: 100% !important;
        display: block !important;
    }
    @media (max-width: 991px) {
        .chart {
            height: 320px !important;
            min-height: 320px !important;
        }
    }
    @media (max-width: 768px) {
        .chart {
            height: 300px !important;
            min-height: 300px !important;
        }
    }
    @media (max-width: 576px) {
        .chart {
            height: 280px !important;
            min-height: 280px !important;
        }
    }
    
    /* Asegurar que todos los widgets tengan la misma altura */
    .widget-card {
        min-height: 180px;
        display: flex;
        flex-direction: column;
    }
    .widget-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .widget-card .card-body > div {
        min-height: 100px;
    }
    
    /* Animaciones para nuevos registros */
    @keyframes pulseNew {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(94, 114, 228, 0.7);
        }
        50% {
            transform: scale(1.02);
            box-shadow: 0 0 0 10px rgba(94, 114, 228, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(94, 114, 228, 0);
        }
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes highlight {
        0%, 100% {
            background-color: transparent;
        }
        50% {
            background-color: rgba(94, 114, 228, 0.1);
        }
    }
    
    .widget-new {
        animation: pulseNew 1.5s ease-in-out;
    }
    
    .widget-content-new {
        animation: slideIn 0.5s ease-out;
    }
    
    .widget-highlight {
        animation: highlight 2s ease-in-out;
    }
    
    /* Badge de "Nuevo" */
    .badge-new {
        position: absolute;
        top: 10px;
        right: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
        text-transform: uppercase;
        z-index: 10;
        animation: pulseNew 2s infinite;
    }
    
    /* Badge de contador en cola */
    .widget-badge-count {
        position: absolute;
        top: 8px;
        right: 8px;
        background: linear-gradient(135deg, #5e72e4 0%, #667eea 100%);
        color: white;
        padding: 6px 10px;
        border-radius: 50%;
        font-size: 12px;
        font-weight: bold;
        min-width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        box-shadow: 0 2px 8px rgba(94, 114, 228, 0.4);
    }
    
    .widget-badge-count.zero {
        background: #e9ecef;
        color: #6c757d;
        box-shadow: none;
    }
</style>
@endpush

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Panel de Control'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white mb-0">Panel de Control</h2>
                <p class="text-white text-sm opacity-8">Resumen general del estado de las reparaciones</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">En Progreso</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $stats['en_progreso'] }}
                                    </h5>
                                    <p class="mb-0">
                                        <span class="text-warning text-sm font-weight-bolder">Reparaciones activas</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle">
                                    <i class="ni ni-settings-gear-65 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Pendientes Revisión</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $stats['pendientes_revision'] }}
                                    </h5>
                                    <p class="mb-0">
                                        <span class="text-info text-sm font-weight-bolder">Equipos recibidos</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="ni ni-archive-2 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Completadas</p>
                                    <h5 class="font-weight-bolder">
                                        {{ $stats['completadas'] }}
                                    </h5>
                                    <p class="mb-0">
                                        <span class="text-success text-sm font-weight-bolder">Este mes</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Próximas Reparaciones -->
        <div class="row mt-4">
            <div class="col-lg-4 mb-4">
                <div class="card widget-card" id="widget-mantenimiento" data-widget-id="{{ $siguienteMantenimiento->id ?? null }}" data-widget-type="mantenimiento">
                    <div class="card-header pb-0 p-3 position-relative">
                        <h6 class="mb-0">Siguiente Mantenimiento</h6>
                        <p class="text-sm text-muted mb-0">Próximo mantenimiento pendiente</p>
                        <span class="widget-badge-count {{ $totalMantenimientos == 0 ? 'zero' : '' }}" id="count-mantenimiento" data-count="{{ $totalMantenimientos }}">
                            {{ $totalMantenimientos }}
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if($siguienteMantenimiento)
                            <div class="d-flex align-items-center widget-content" data-reparacion-id="{{ $siguienteMantenimiento->id }}">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle me-3">
                                    <i class="ni ni-settings-gear-65 text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $siguienteMantenimiento->codigo_reparacion }}</h6>
                                    <p class="text-sm mb-0">{{ $siguienteMantenimiento->equipo->cliente->nombre ?? 'N/A' }}</p>
                                    <p class="text-xs text-muted mb-0">
                                        <i class="fas fa-calendar me-1"></i>
                                        Recibido: {{ $siguienteMantenimiento->fecha_ingreso ? $siguienteMantenimiento->fecha_ingreso->format('d/m/Y') : 'N/A' }}
                                    </p>
                                    <x-badge-estado :estado="$siguienteMantenimiento->estado" size="sm" />
                                </div>
                                <a href="{{ route('reparaciones.index', ['tipo_servicio' => 'mantenimiento', 'estado' => 'pendientes']) }}" class="btn btn-sm btn-primary">
                                    Ver Cola ({{ $totalMantenimientos }})
                                </a>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="min-height: 100px;">
                                <p class="text-muted text-sm mb-0">No hay mantenimientos pendientes</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card widget-card" id="widget-reparacion" data-widget-id="{{ $siguienteReparacion->id ?? null }}" data-widget-type="reparacion">
                    <div class="card-header pb-0 p-3 position-relative">
                        <h6 class="mb-0">Siguiente Reparación</h6>
                        <p class="text-sm text-muted mb-0">Próxima reparación pendiente</p>
                        <span class="widget-badge-count {{ $totalReparaciones == 0 ? 'zero' : '' }}" id="count-reparacion" data-count="{{ $totalReparaciones }}">
                            {{ $totalReparaciones }}
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if($siguienteReparacion)
                            <div class="d-flex align-items-center widget-content" data-reparacion-id="{{ $siguienteReparacion->id }}">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle me-3">
                                    <i class="ni ni-tools text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $siguienteReparacion->codigo_reparacion }}</h6>
                                    <p class="text-sm mb-0">{{ $siguienteReparacion->equipo->cliente->nombre ?? 'N/A' }}</p>
                                    <p class="text-xs text-muted mb-0">
                                        <i class="fas fa-calendar me-1"></i>
                                        Recibido: {{ $siguienteReparacion->fecha_ingreso ? $siguienteReparacion->fecha_ingreso->format('d/m/Y') : 'N/A' }}
                                    </p>
                                    <x-badge-estado :estado="$siguienteReparacion->estado" size="sm" />
                                </div>
                                <a href="{{ route('reparaciones.index', ['tipo_servicio' => 'reparacion', 'excluir_gpu' => '1', 'estado' => 'pendientes']) }}" class="btn btn-sm btn-primary">
                                    Ver Cola ({{ $totalReparaciones }})
                                </a>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="min-height: 100px;">
                                <p class="text-muted text-sm mb-0">No hay reparaciones pendientes</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card widget-card" id="widget-gpu" data-widget-id="{{ $siguienteGPU->id ?? null }}" data-widget-type="gpu">
                    <div class="card-header pb-0 p-3 position-relative">
                        <h6 class="mb-0">Siguiente GPU</h6>
                        <p class="text-sm text-muted mb-0">Próxima reparación de GPU</p>
                        <span class="widget-badge-count {{ $totalGPUs == 0 ? 'zero' : '' }}" id="count-gpu" data-count="{{ $totalGPUs }}">
                            {{ $totalGPUs }}
                        </span>
                    </div>
                    <div class="card-body p-3">
                        @if($siguienteGPU)
                            <div class="d-flex align-items-center widget-content" data-reparacion-id="{{ $siguienteGPU->id }}">
                                <div class="icon icon-shape bg-gradient-warning shadow-warning text-center rounded-circle me-3">
                                    <i class="ni ni-laptop text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $siguienteGPU->codigo_reparacion }}</h6>
                                    <p class="text-sm mb-0">{{ $siguienteGPU->equipo->cliente->nombre ?? 'N/A' }}</p>
                                    <p class="text-xs text-muted mb-0">
                                        <i class="fas fa-calendar me-1"></i>
                                        Recibido: {{ $siguienteGPU->fecha_ingreso ? $siguienteGPU->fecha_ingreso->format('d/m/Y') : 'N/A' }}
                                    </p>
                                    <x-badge-estado :estado="$siguienteGPU->estado" size="sm" />
                                </div>
                                <a href="{{ route('reparaciones.index', ['tipo_servicio' => 'reparacion', 'tipo_equipo' => 'gpu', 'estado' => 'pendientes']) }}" class="btn btn-sm btn-primary">
                                    Ver Cola ({{ $totalGPUs }})
                                </a>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="min-height: 100px;">
                                <p class="text-muted text-sm mb-0">No hay GPUs pendientes</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Widget de Garantías -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Garantías</h6>
                        <p class="text-sm text-muted mb-0">Resumen de reparaciones en garantía</p>
                    </div>
                    <div class="card-body p-3">
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle me-3">
                                    <i class="ni ni-shield text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">Total de Garantías</h6>
                                    <p class="text-sm mb-0 text-muted">{{ $stats['total_garantias'] }} reparaciones</p>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-sm text-danger">
                                    <i class="fas fa-exclamation-circle me-1"></i>Vencidas
                                </span>
                                <span class="badge badge-sm bg-gradient-danger">{{ $stats['garantias_vencidas'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-sm text-warning">
                                    <i class="fas fa-clock me-1"></i>Por vencer (7 días)
                                </span>
                                <span class="badge badge-sm bg-gradient-warning">{{ $stats['garantias_por_vencer'] }}</span>
                            </div>
                        </div>
                        <a href="{{ route('reparaciones.index', ['es_garantia' => 1]) }}" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-list me-1"></i>Ver todas las garantías
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <!-- Gráfico de Reparaciones por Mes -->
            <div class="col-12 col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="mb-0">Reparaciones por Mes</h6>
                                <p class="text-sm text-muted mb-0">Últimos 6 meses</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 p-md-4">
                        <div class="chart" style="position: relative; height: 350px; width: 100%; min-height: 300px;">
                            <canvas id="chart-pie" class="chart-canvas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="col-12 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-0">Actividad Reciente</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="timeline timeline-one-side">
                            @forelse($actividadReciente as $actividad)
                            <div class="timeline-block mb-3">
                                <span class="timeline-step">
                                    <i class="ni ni-bell-55 text-{{ $actividad->estado == 'Finalizado' ? 'success' : ($actividad->estado == 'Esperando Pieza' ? 'warning' : 'info') }}"></i>
                                </span>
                                <div class="timeline-content">
                                    <h6 class="text-dark text-sm font-weight-bold mb-0">
                                        @if($actividad->reparacion)
                                            <a href="{{ route('reparaciones.show', $actividad->reparacion) }}" class="text-primary">
                                                {{ $actividad->reparacion->codigo_reparacion }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </h6>
                                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">
                                        {{ $actividad->estado }}
                                    </p>
                                    <p class="text-sm mt-1 mb-0">
                                        {{ Str::limit($actividad->comentario ?? 'Sin comentario', 60) }}
                                    </p>
                                    <p class="text-xs text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $actividad->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-sm text-center py-3">No hay actividad reciente</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>

    @push('js')
    <script src="/assets/js/plugins/chartjs.min.js"></script>
    <script>
        // Sistema de detección de nuevos registros y animaciones
        (function() {
            let widgetStates = {
                mantenimiento: {{ $siguienteMantenimiento->id ?? 'null' }},
                reparacion: {{ $siguienteReparacion->id ?? 'null' }},
                gpu: {{ $siguienteGPU->id ?? 'null' }}
            };
            
            let widgetCounts = {
                mantenimiento: {{ $totalMantenimientos }},
                reparacion: {{ $totalReparaciones }},
                gpu: {{ $totalGPUs }}
            };
            
            // Función para actualizar contadores
            function updateCounters(data) {
                if (data.total_mantenimientos !== undefined) {
                    const countEl = document.getElementById('count-mantenimiento');
                    if (countEl) {
                        const newCount = data.total_mantenimientos;
                        countEl.textContent = newCount;
                        countEl.setAttribute('data-count', newCount);
                        if (newCount === 0) {
                            countEl.classList.add('zero');
                        } else {
                            countEl.classList.remove('zero');
                        }
                        widgetCounts.mantenimiento = newCount;
                    }
                }
                if (data.total_reparaciones !== undefined) {
                    const countEl = document.getElementById('count-reparacion');
                    if (countEl) {
                        const newCount = data.total_reparaciones;
                        countEl.textContent = newCount;
                        countEl.setAttribute('data-count', newCount);
                        if (newCount === 0) {
                            countEl.classList.add('zero');
                        } else {
                            countEl.classList.remove('zero');
                        }
                        widgetCounts.reparacion = newCount;
                    }
                }
                if (data.total_gpus !== undefined) {
                    const countEl = document.getElementById('count-gpu');
                    if (countEl) {
                        const newCount = data.total_gpus;
                        countEl.textContent = newCount;
                        countEl.setAttribute('data-count', newCount);
                        if (newCount === 0) {
                            countEl.classList.add('zero');
                        } else {
                            countEl.classList.remove('zero');
                        }
                        widgetCounts.gpu = newCount;
                    }
                }
            }
            
            // Función para animar widget cuando hay un nuevo registro
            function animateWidget(widgetType) {
                const widget = document.getElementById('widget-' + widgetType);
                if (!widget) return;
                
                // Remover clases anteriores
                widget.classList.remove('widget-new', 'widget-highlight');
                
                // Forzar reflow
                void widget.offsetWidth;
                
                // Agregar animaciones
                widget.classList.add('widget-new', 'widget-highlight');
                
                // Agregar badge "Nuevo" temporalmente
                const badge = document.createElement('span');
                badge.className = 'badge-new';
                badge.textContent = 'Nuevo';
                badge.id = 'badge-new-' + widgetType;
                
                // Remover badge anterior si existe
                const existingBadge = document.getElementById('badge-new-' + widgetType);
                if (existingBadge) {
                    existingBadge.remove();
                }
                
                widget.querySelector('.card-header').appendChild(badge);
                
                // Remover badge después de 5 segundos
                setTimeout(() => {
                    if (badge.parentNode) {
                        badge.style.transition = 'opacity 0.5s';
                        badge.style.opacity = '0';
                        setTimeout(() => badge.remove(), 500);
                    }
                }, 5000);
                
                // Remover clases de animación después de que termine
                setTimeout(() => {
                    widget.classList.remove('widget-new', 'widget-highlight');
                }, 2000);
            }
            
            // Verificar cambios cada 10 segundos (más frecuente para mejor respuesta)
            function checkForNewRepairs() {
                const url = '{{ route("dashboard.check") }}';
                const timestamp = new Date().getTime(); // Evitar caché del navegador
                
                fetch(url + '?t=' + timestamp, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Actualizar contadores siempre (pueden cambiar aunque no haya nuevo siguiente)
                    if (data.total_mantenimientos !== undefined || 
                        data.total_reparaciones !== undefined || 
                        data.total_gpus !== undefined) {
                        updateCounters(data);
                    }
                    
                    // Verificar si cambió el siguiente registro
                    if (data.mantenimiento !== widgetStates.mantenimiento) {
                        const oldValue = widgetStates.mantenimiento;
                        widgetStates.mantenimiento = data.mantenimiento;
                        // Solo animar si hay un nuevo registro (no si se eliminó)
                        if (data.mantenimiento && data.mantenimiento !== oldValue) {
                            animateWidget('mantenimiento');
                        }
                    }
                    if (data.reparacion !== widgetStates.reparacion) {
                        const oldValue = widgetStates.reparacion;
                        widgetStates.reparacion = data.reparacion;
                        if (data.reparacion && data.reparacion !== oldValue) {
                            animateWidget('reparacion');
                        }
                    }
                    if (data.gpu !== widgetStates.gpu) {
                        const oldValue = widgetStates.gpu;
                        widgetStates.gpu = data.gpu;
                        if (data.gpu && data.gpu !== oldValue) {
                            animateWidget('gpu');
                        }
                    }
                })
                .catch(error => {
                    // Error silencioso - solo registrar en desarrollo
                    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                        console.error('Error al verificar nuevos registros:', error);
                    }
                });
            }
            
            // Iniciar verificación periódica (cada 10 segundos para mejor respuesta)
            let checkInterval = setInterval(checkForNewRepairs, 10000);
            
            // Ejecutar inmediatamente al cargar la página (después de 2 segundos)
            setTimeout(checkForNewRepairs, 2000);
            
            // También verificar cuando la página vuelve a tener foco
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    checkForNewRepairs();
                }
            });
            
            // Limpiar intervalo cuando se sale de la página
            window.addEventListener('beforeunload', function() {
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
            });
        })();
        
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById("chart-pie");
            if (!ctx) {
                return; // Silencioso - elemento no encontrado
            }

            ctx = ctx.getContext("2d");
            if (!ctx) {
                return; // Silencioso - contexto no disponible
            }

            // Colores para cada mes
            var colores = [
                'rgba(94, 114, 228, 0.8)',   // Azul
                'rgba(23, 193, 232, 0.8)',   // Cian
                'rgba(82, 95, 225, 0.8)',    // Índigo
                'rgba(251, 99, 64, 0.8)',    // Naranja
                'rgba(34, 197, 94, 0.8)',    // Verde
                'rgba(245, 101, 101, 0.8)'   // Rojo
            ];

            var chartData = {
                labels: [
                    @foreach($reparacionesPorMes as $mes)
                        "{{ $mes['mes'] }}",
                    @endforeach
                ],
                datasets: [{
                    label: "Reparaciones",
                    data: [
                        @foreach($reparacionesPorMes as $mes)
                            {{ $mes['count'] }},
                        @endforeach
                    ],
                    backgroundColor: colores,
                    borderColor: [
                        'rgba(94, 114, 228, 1)',
                        'rgba(23, 193, 232, 1)',
                        'rgba(82, 95, 225, 1)',
                        'rgba(251, 99, 64, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(245, 101, 101, 1)'
                    ],
                    borderWidth: 2,
                    hoverOffset: 4
                }],
            };

            try {
                var chart = new Chart(ctx, {
                    type: "pie",
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: {
                                top: 15,
                                right: 15,
                                bottom: 15,
                                left: 15
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        family: "Open Sans",
                                        weight: '500'
                                    },
                                    color: '#67748e'
                                }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 12,
                                titleFont: {
                                    size: 13,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 12
                                },
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        var label = context.label || '';
                                        var value = context.parsed || 0;
                                        var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: true,
                            mode: 'point',
                        }
                    },
                });
                
                // Forzar redimensionamiento después de un breve delay y en resize
                setTimeout(function() {
                    if (chart) {
                        chart.resize();
                    }
                }, 100);
                
                // Redimensionar en cambio de tamaño de ventana
                window.addEventListener('resize', function() {
                    if (chart) {
                        chart.resize();
                    }
                });
            } catch (error) {
                console.error('Error al inicializar el gráfico:', error);
            }
        });
    </script>
    @endpush
@endsection

