@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<style>
    /* Estilos específicos de esta vista - usar design-system.css cuando sea posible */
    .table-responsive-custom {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    @media (min-width: 992px) {
        .desktop-only .form-label {
            white-space: nowrap;
            overflow: visible;
            text-overflow: ellipsis;
            display: block;
        }
    }
    
    /* Botones responsive en tabla */
    .col-acciones {
        min-width: 100px;
        white-space: nowrap;
    }
    
    .col-acciones .btn {
        min-width: 36px;
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    @media (max-width: 991.98px) {
        .col-acciones {
            min-width: 80px;
        }
        
        .col-acciones .btn {
            min-width: 32px;
            padding: 0.25rem 0.4rem;
            font-size: 0.75rem;
        }
    }
    
    /* Fixed plugin responsive */
    .fixed-plugin {
        display: block !important;
    }
    
    .fixed-plugin-button {
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
    }
    
    @media (max-width: 767.98px) {
        .fixed-plugin-button {
            width: 44px;
            height: 44px;
            bottom: 80px !important; /* Evitar conflicto con FAB */
        }
    }

    .btn-cta {
        background: #ff7a18;
        border-color: #ff7a18;
        color: #fff;
        box-shadow: 0 10px 24px rgba(255, 122, 24, 0.24);
    }

    .btn-cta:hover,
    .btn-cta:focus {
        background: #e96808;
        border-color: #e96808;
        color: #fff;
    }
</style>
@endpush

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Reparaciones'])
    
    <div class="container-fluid py-4">
        
        <!-- Mobile Header -->
        <div class="mobile-only mobile-header d-flex justify-content-between align-items-start mb-4">
            <div>
                <h2>Reparaciones</h2>
                <p>Gestión de órdenes</p>
            </div>
            <a href="{{ route('reparaciones.create') }}" class="fab">
                <i class="fas fa-plus fa-lg"></i>
            </a>
        </div>

        <!-- Desktop Header -->
        <div class="desktop-only d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="text-white mb-0">Gestión de Reparaciones</h2>
                <p class="text-white opacity-8 mb-0">
                    @if(request('estado') == 'pendientes')
                        @if(request('tipo_servicio') == 'mantenimiento')
                            Cola de Mantenimientos Pendientes
                        @elseif(request('tipo_equipo') == 'gpu')
                            Cola de GPUs Pendientes
                        @elseif(request('excluir_gpu') == '1')
                            Cola de Reparaciones Pendientes (sin GPUs)
                        @else
                            Cola de Reparaciones Pendientes
                        @endif
                    @else
                        Administre todas las órdenes de reparación
                    @endif
                </p>
            </div>
            <a href="{{ route('reparaciones.create') }}" class="btn btn-cta">
                <i class="fas fa-plus me-2"></i>Nueva Reparación
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(request('estado') == 'pendientes')
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Viendo cola de reparaciones pendientes:</strong> 
            @if(request('tipo_servicio') == 'mantenimiento')
                Mostrando todos los mantenimientos en cola. 
            @elseif(request('tipo_equipo') == 'gpu')
                Mostrando todas las GPUs en cola. 
            @elseif(request('excluir_gpu') == '1')
                Mostrando todas las reparaciones en cola, excluyendo GPUs. 
            @else
                Mostrando todas las reparaciones en cola. 
            @endif
            Puede navegar entre todas las reparaciones usando la paginación.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <!-- Filters Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('reparaciones.index') }}" id="filterForm">
                    
                    <!-- Mobile Filters -->
                    <div class="mobile-only">
                        <div class="search-bar-mobile mb-3">
                            <span class="search-icon"><i class="fas fa-search"></i></span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="Buscar por ID o cliente..."
                                   value="{{ request('search') }}">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold mb-1">Fecha Desde</label>
                                <input type="date" name="fecha_desde" class="form-control form-control-sm" 
                                       value="{{ request('fecha_desde') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold mb-1">Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control form-control-sm" 
                                       value="{{ request('fecha_hasta') }}">
                            </div>
                        </div>

                        <div class="filter-chips-container mb-3">
                            <button type="button" onclick="setFilter('')"
                                    class="filter-chip {{ !request('estado') ? 'active' : '' }}">
                                Todos
                            </button>
                            <button type="button" onclick="setFilter('En Diagnóstico')"
                                    class="filter-chip {{ request('estado') == 'En Diagnóstico' ? 'active' : '' }}">
                                Diagnóstico
                            </button>
                            <button type="button" onclick="setFilter('Pendiente Revisión Admin')"
                                    class="filter-chip {{ request('estado') == 'Pendiente Revisión Admin' ? 'active' : '' }}">
                                Rev. Admin
                            </button>
                            <button type="button" onclick="setFilter('En Proceso')"
                                    class="filter-chip {{ request('estado') == 'En Proceso' ? 'active' : '' }}">
                                En Proceso
                            </button>
                            <button type="button" onclick="setFilter('Finalizado')"
                                    class="filter-chip {{ request('estado') == 'Finalizado' ? 'active' : '' }}">
                                Completado
                            </button>
                            <button type="button" onclick="setFilter('Sin Reparación')"
                                    class="filter-chip {{ request('estado') == 'Sin Reparación' ? 'active' : '' }}">
                                Sin Reparación
                            </button>
                            <button type="button" onclick="setFilter('Cancelado')"
                                    class="filter-chip {{ request('estado') == 'Cancelado' ? 'active' : '' }}">
                                Cancelado
                            </button>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold mb-1">Ordenar</label>
                            <select name="orden" class="form-select form-select-sm">
                                <option value="asc" {{ request('orden', 'desc') == 'asc' ? 'selected' : '' }}>Asc</option>
                                <option value="desc" {{ request('orden', 'desc') == 'desc' ? 'selected' : '' }}>Desc</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Aplicar
                            </button>
                            @if(request()->has('search') || request()->has('estado') || request()->has('fecha_desde') || request()->has('fecha_hasta') || request()->has('orden'))
                            <a href="{{ route('reparaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times me-2"></i>Limpiar Filtros
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Desktop Filters - Compacto -->
                    <div class="desktop-only">
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label small mb-1" style="font-size: 0.75rem;">Buscar</label>
                                <input type="text" name="search" class="form-control form-control-sm" 
                                       placeholder="ID, cliente..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1" style="font-size: 0.75rem;">Estado</label>
                                <select name="estado" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <option value="Recibido" {{ request('estado') == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                                    <option value="En Diagnóstico" {{ request('estado') == 'En Diagnóstico' ? 'selected' : '' }}>Diagnóstico</option>
                                    <option value="Pendiente Revisión Admin" {{ request('estado') == 'Pendiente Revisión Admin' ? 'selected' : '' }}>Pendiente Rev. Admin</option>
                                    <option value="Esperando Pieza" {{ request('estado') == 'Esperando Pieza' ? 'selected' : '' }}>Esperando Pieza</option>
                                    <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                    <option value="Finalizado" {{ request('estado') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                    <option value="Sin Reparación" {{ request('estado') == 'Sin Reparación' ? 'selected' : '' }}>Sin Reparación</option>
                                    <option value="Entregado" {{ request('estado') == 'Entregado' ? 'selected' : '' }}>Entregado</option>
                                    <option value="Cancelado" {{ request('estado') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1" style="font-size: 0.75rem;">Técnico</label>
                                <select name="tecnico_id" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    @foreach($tecnicos ?? [] as $tecnico)
                                        <option value="{{ $tecnico->id }}" {{ request('tecnico_id') == $tecnico->id ? 'selected' : '' }}>
                                            {{ $tecnico->firstname }} {{ $tecnico->lastname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1" style="font-size: 0.75rem;">Fecha Desde</label>
                                <input type="date" name="fecha_desde" class="form-control form-control-sm" 
                                       value="{{ request('fecha_desde') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small mb-1" style="font-size: 0.75rem;">Fecha Hasta</label>
                                <input type="date" name="fecha_hasta" class="form-control form-control-sm" 
                                       value="{{ request('fecha_hasta') }}">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small mb-1" style="font-size: 0.75rem;">Orden</label>
                                <select name="orden" class="form-select form-select-sm">
                                    <option value="asc" {{ request('orden', 'desc') == 'asc' ? 'selected' : '' }}>Asc</option>
                                    <option value="desc" {{ request('orden', 'desc') == 'desc' ? 'selected' : '' }}>Desc</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="es_garantia" id="es_garantia_filter_desktop" value="1" {{ request('es_garantia') ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="es_garantia_filter_desktop" style="font-size: 0.75rem;">
                                        Solo garantías
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label small mb-1" style="font-size: 0.75rem; visibility: hidden;">Botón</label>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    Aplicar
                                </button>
                            </div>
                        </div>
                        @if(request()->has('search') || request()->has('estado') || request()->has('tecnico_id') || request()->has('fecha_desde') || request()->has('fecha_hasta') || request()->has('orden') || request()->has('es_garantia'))
                        <div class="row mt-2">
                            <div class="col-12">
                                <a href="{{ route('reparaciones.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times me-1"></i>Limpiar Filtros
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <input type="hidden" name="estado" id="estadoFilter" value="{{ request('estado') }}">
                </form>
            </div>
        </div>

        <!-- Mobile: Card List -->
        <div class="mobile-only">
            @forelse($reparaciones as $reparacion)
                <div class="repair-card-mobile" onclick="window.location.href='{{ route('reparaciones.show', $reparacion->id) }}'">
                    <div class="repair-card-header">
                        <span class="repair-id">{{ $reparacion->codigo_reparacion }}</span>
                        <x-badge-estado :estado="$reparacion->estado" size="sm" />
                    </div>

                    <div class="d-flex align-items-center gap-3 mb-3">
                        @php
                            $tieneFotos = $reparacion->equipo->fotos && $reparacion->equipo->fotos->count() > 0;
                        @endphp
                        @if($tieneFotos)
                            @php
                                $fotosUrls = $reparacion->equipo->fotos->map(function($foto) {
                                    return ['url' => \Storage::url($foto->ruta)];
                                })->toArray();
                                $primeraFoto = $reparacion->equipo->fotos->first();
                            @endphp
                            <a href="{{ Storage::url($primeraFoto->ruta) }}" target="_blank" onclick="event.stopPropagation();" style="position: relative; display: block; cursor: pointer;">
                                <img src="{{ Storage::url($primeraFoto->ruta) }}" 
                                     class="rounded" 
                                     alt="Foto del equipo"
                                     style="width: 60px; height: 60px; object-fit: cover; border: 2px solid #e9ecef;"
                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'repair-device-icon\'><i class=\'fas fa-image text-primary fa-lg\'></i></div>';">
                                @if($reparacion->equipo->fotos->count() > 1)
                                    <span class="badge bg-primary" style="position: absolute; top: -5px; right: -5px; font-size: 0.7rem;">
                                        +{{ $reparacion->equipo->fotos->count() - 1 }}
                                    </span>
                                @endif
                            </a>
                        @else
                            <div class="repair-device-icon">
                                <i class="fas 
                                    @if(str_contains(strtolower($reparacion->equipo->tipo ?? ''), 'laptop')) fa-laptop
                                    @elseif(str_contains(strtolower($reparacion->equipo->tipo ?? ''), 'pc')) fa-desktop
                                    @elseif(str_contains(strtolower($reparacion->equipo->tipo ?? ''), 'gpu') || str_contains(strtolower($reparacion->equipo->tipo ?? ''), 'gráfica')) fa-microchip
                                    @elseif(str_contains(strtolower($reparacion->equipo->tipo ?? ''), 'consola')) fa-gamepad
                                    @else fa-server
                                    @endif text-primary fa-lg"></i>
                            </div>
                        @endif
                        <div class="repair-info">
                            <div class="d-flex align-items-center gap-2">
                                <div class="repair-client-name">{{ $reparacion->equipo->cliente->nombre ?? 'N/A' }}</div>
                                @if($reparacion->es_garantia)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                        <i class="fas fa-shield-alt me-1"></i>GARANTÍA
                                    </span>
                                    @if($reparacion->fecha_vencimiento_garantia)
                                        @php
                                            $diasRestantes = (int) \Carbon\Carbon::now()->diffInDays($reparacion->fecha_vencimiento_garantia, false);
                                            $estadoGarantia = null;
                                            if ($diasRestantes < 0) {
                                                $estadoGarantia = 'vencida';
                                                $diasRestantes = abs($diasRestantes);
                                            } elseif ($diasRestantes <= 7) {
                                                $estadoGarantia = 'por_vencer';
                                            } else {
                                                $estadoGarantia = 'vigente';
                                            }
                                        @endphp
                                        <x-badge-garantia :diasRestantes="$diasRestantes" :estadoGarantia="$estadoGarantia" size="xs" />
                                    @endif
                                @endif
                            </div>
                            <div class="repair-device-name">{{ $reparacion->equipo->marca }} {{ $reparacion->equipo->modelo }}</div>
                        </div>
                    </div>

                    <div class="repair-card-footer">
                        <span>
                            <i class="fas fa-calendar-alt me-1"></i>
                            {{ $reparacion->fecha_ingreso ? $reparacion->fecha_ingreso->format('d/m/Y') : 'N/A' }}
                        </span>
                        @if($reparacion->tecnico)
                            <span><i class="fas fa-user-cog me-1"></i>{{ $reparacion->tecnico->firstname }}</span>
                        @endif
                        <span class="fw-bold text-primary">${{ number_format($reparacion->total_estimado, 2) }}</span>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No se encontraron reparaciones</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Desktop: Table -->
        <div class="desktop-only card">
            <div class="table-responsive-custom">
                <table class="table align-items-center mb-0 table-reparaciones">
                    <thead>
                        <tr>
                            <th class="col-foto">Foto</th>
                            <th class="col-codigo">Código</th>
                            <th class="col-cliente">Cliente</th>
                            <th class="col-equipo">Equipo</th>
                            <th class="col-estado">Estado</th>
                            <th class="col-tecnico">Técnico</th>
                            <th class="col-fecha">Fecha</th>
                            <th class="col-total">Total</th>
                            <th class="col-garantia">Garantía</th>
                            <th class="col-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reparaciones as $reparacion)
                            <tr class="cursor-pointer" onclick="window.location.href='{{ route('reparaciones.show', $reparacion->id) }}'">
                                <td>
                                    @php
                                        $tieneFotos = $reparacion->equipo->fotos && $reparacion->equipo->fotos->count() > 0;
                                    @endphp
                                    @if($tieneFotos)
                                        @php
                                            $fotosUrls = $reparacion->equipo->fotos->map(function($foto) {
                                                return ['url' => \Storage::url($foto->ruta)];
                                            })->toArray();
                                            $primeraFoto = $reparacion->equipo->fotos->first();
                                        @endphp
                                        <a href="{{ Storage::url($primeraFoto->ruta) }}" target="_blank" onclick="event.stopPropagation();" class="d-flex align-items-center" style="position: relative; cursor: pointer; text-decoration: none;">
                                            <img src="{{ Storage::url($primeraFoto->ruta) }}" 
                                                 class="rounded" 
                                                 alt="Foto del equipo"
                                                 style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #e9ecef;"
                                                 onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'d-flex align-items-center justify-content-center\' style=\'width: 50px; height: 50px; background: #f8f9fa; border-radius: 8px;\'><i class=\'fas fa-image text-secondary\'></i></div>';">
                                            @if($reparacion->equipo->fotos->count() > 1)
                                                <span class="badge bg-primary ms-1" style="font-size: 0.65rem;">
                                                    +{{ $reparacion->equipo->fotos->count() - 1 }}
                                                </span>
                                            @endif
                                        </a>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #f8f9fa; border-radius: 8px;">
                                            <i class="fas fa-image text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="col-codigo"><p class="text-xs font-weight-bold mb-0">{{ $reparacion->codigo_reparacion }}</p></td>
                                <td class="col-cliente"><p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->cliente->nombre ?? 'N/A' }}</p></td>
                                <td class="col-equipo">
                                    <p class="text-sm mb-0">{{ $reparacion->equipo->marca ?? 'N/A' }}</p>
                                    <p class="text-xs text-secondary mb-0">{{ $reparacion->equipo->modelo ?? '' }}</p>
                                </td>
                                <td class="col-estado">
                                    <x-badge-estado :estado="$reparacion->estado" size="sm" />
                                </td>
                                <td class="col-tecnico">
                                    <p class="text-sm mb-0">
                                        {{ $reparacion->tecnico ? $reparacion->tecnico->firstname . ' ' . $reparacion->tecnico->lastname : 'Sin asignar' }}
                                    </p>
                                </td>
                                <td class="col-fecha"><p class="text-sm mb-0">{{ $reparacion->fecha_ingreso ? $reparacion->fecha_ingreso->format('d/m/Y') : 'N/A' }}</p></td>
                                <td class="col-total"><p class="text-sm font-weight-bold text-primary mb-0">${{ number_format($reparacion->total_estimado, 2) }}</p></td>
                                <td class="col-garantia">
                                    <x-garantia-info :reparacion="$reparacion" />
                                </td>
                                <td class="col-acciones" onclick="event.stopPropagation();">
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        <a href="{{ route('reparaciones.show', $reparacion->id) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('reparaciones.etiqueta-entrada', $reparacion) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Imprimir etiqueta">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('reparaciones.edit', $reparacion->id) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           data-bs-toggle="tooltip" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-2 d-block"></i>
                                    <p class="text-secondary">No se encontraron reparaciones</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($reparaciones->hasPages())
        <div class="mt-4" style="padding-bottom: 5rem;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <!-- Información de resultados -->
                <div class="text-sm text-secondary">
                    Mostrando {{ $reparaciones->firstItem() }} a {{ $reparaciones->lastItem() }} de {{ $reparaciones->total() }} resultados
                </div>
                
                <!-- Navegación de páginas -->
                <nav aria-label="Navegación de páginas">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Botón Anterior --}}
                        @if($reparaciones->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link border-0">
                                    <i class="fas fa-chevron-left fa-xs"></i>
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link border-0" href="{{ $reparaciones->previousPageUrl() }}" aria-label="Anterior">
                                    <i class="fas fa-chevron-left fa-xs"></i>
                                </a>
                            </li>
                        @endif

                        {{-- Números de página --}}
                        @php
                            $currentPage = $reparaciones->currentPage();
                            $lastPage = $reparaciones->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

                        @if($startPage > 1)
                            <li class="page-item">
                                <a class="page-link border-0" href="{{ $reparaciones->url(1) }}">1</a>
                            </li>
                            @if($startPage > 2)
                                <li class="page-item disabled">
                                    <span class="page-link border-0">...</span>
                                </li>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page == $currentPage)
                                <li class="page-item active">
                                    <span class="page-link border-0">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link border-0" href="{{ $reparaciones->url($page) }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <li class="page-item disabled">
                                    <span class="page-link border-0">...</span>
                                </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link border-0" href="{{ $reparaciones->url($lastPage) }}">{{ $lastPage }}</a>
                            </li>
                        @endif

                        {{-- Botón Siguiente --}}
                        @if($reparaciones->hasMorePages())
                            <li class="page-item">
                                <a class="page-link border-0" href="{{ $reparaciones->nextPageUrl() }}" aria-label="Siguiente">
                                    <i class="fas fa-chevron-right fa-xs"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link border-0">
                                    <i class="fas fa-chevron-right fa-xs"></i>
                                </span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal para Zoom de Imágenes -->
    <script>
        function setFilter(estado) {
            document.getElementById('estadoFilter').value = estado;
            // Preservar otros filtros al cambiar estado desde chips móviles
            const form = document.getElementById('filterForm');
            const fechaDesde = form.querySelector('[name="fecha_desde"]');
            const fechaHasta = form.querySelector('[name="fecha_hasta"]');
            const orden = form.querySelector('[name="orden"]');
            const search = form.querySelector('[name="search"]');
            
            // Si no hay valores, mantener los valores actuales de la URL
            if (fechaDesde && !fechaDesde.value) {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('fecha_desde')) {
                    fechaDesde.value = urlParams.get('fecha_desde');
                }
            }
            if (fechaHasta && !fechaHasta.value) {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('fecha_hasta')) {
                    fechaHasta.value = urlParams.get('fecha_hasta');
                }
            }
            if (orden && !orden.value) {
                const urlParams = new URLSearchParams(window.location.search);
                orden.value = urlParams.get('orden') || 'asc';
            }
            
            form.submit();
        }
    </script>
@endsection
