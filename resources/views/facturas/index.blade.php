@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Facturas'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">Listado de Facturas</h2>
                    <p class="text-white text-sm opacity-8">Gestión completa de facturas emitidas</p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Filtros y Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('facturas.index') }}">
                            <div class="row align-items-end">
                                <!-- Búsqueda -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Buscar</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Número de factura, cliente..." 
                                           value="{{ request('search') }}">
                                </div>

                                <!-- Filtro por Fecha Inicio -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Fecha Desde</label>
                                    <input type="date" name="fecha_inicio" class="form-control" 
                                           value="{{ request('fecha_inicio') }}">
                                </div>

                                <!-- Filtro por Fecha Fin -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Fecha Hasta</label>
                                    <input type="date" name="fecha_fin" class="form-control" 
                                           value="{{ request('fecha_fin') }}">
                                </div>

                                <!-- Botones -->
                                <div class="col-md-2 mb-3">
                                    <button type="submit" class="btn btn-primary w-100 mb-0">
                                        <i class="fas fa-filter me-1"></i>Filtrar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Facturas -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>Facturas ({{ $facturas->total() }})</h6>
                            @if(request()->has('search') || request()->has('fecha_inicio') || request()->has('fecha_fin'))
                            <a href="{{ route('facturas.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar Filtros
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Número</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Equipo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Fecha</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Subtotal</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Impuestos</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-end">Total</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($facturas as $factura)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $factura->numero_factura }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $factura->cliente->nombre ?? 'N/A' }}</h6>
                                                    @if($factura->cliente && $factura->cliente->telefono)
                                                    <p class="text-xs text-secondary mb-0">{{ $factura->cliente->telefono }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">
                                                        {{ $factura->equipo->tipo ?? 'N/A' }}
                                                    </h6>
                                                    @if($factura->equipo)
                                                    <p class="text-xs text-secondary mb-0">
                                                        {{ $factura->equipo->marca ?? '' }} {{ $factura->equipo->modelo ?? '' }}
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                {{ $factura->fecha_emision->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                ${{ number_format($factura->subtotal, 2) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                ${{ number_format($factura->impuestos, 2) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-success text-xs font-weight-bold">
                                                ${{ number_format($factura->total, 2) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <a href="{{ route('facturas.show', $factura) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Ver Factura">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if(auth()->check() && auth()->user()->hasRole('administrador'))
                                                <a href="{{ route('facturas.edit', $factura) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Editar Factura">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif
                                                <a href="{{ route('facturas.pdf', $factura) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   target="_blank"
                                                   data-bs-toggle="tooltip" 
                                                   title="Descargar PDF">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @if($factura->cliente && $factura->cliente->telefono)
                                                <a href="{{ route('facturas.whatsapp', $factura) }}" 
                                                   class="btn btn-sm btn-outline-success" 
                                                   target="_blank"
                                                   data-bs-toggle="tooltip" 
                                                   title="Compartir por WhatsApp">
                                                    <i class="fab fa-whatsapp"></i>
                                                </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-muted mb-0">No se encontraron facturas</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($facturas->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <!-- Información de resultados -->
                            <div class="text-sm text-secondary">
                                Mostrando {{ $facturas->firstItem() }} a {{ $facturas->lastItem() }} de {{ $facturas->total() }} resultados
                            </div>
                            
                            <!-- Navegación de páginas -->
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Botón Anterior --}}
                                    @if($facturas->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link border-0">
                                                <i class="fas fa-chevron-left fa-xs"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $facturas->previousPageUrl() }}" aria-label="Anterior">
                                                <i class="fas fa-chevron-left fa-xs"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Números de página --}}
                                    @php
                                        $currentPage = $facturas->currentPage();
                                        $lastPage = $facturas->lastPage();
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($lastPage, $currentPage + 2);
                                    @endphp

                                    @if($startPage > 1)
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $facturas->url(1) }}">1</a>
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
                                                <a class="page-link border-0" href="{{ $facturas->url($page) }}">{{ $page }}</a>
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
                                            <a class="page-link border-0" href="{{ $facturas->url($lastPage) }}">{{ $lastPage }}</a>
                                        </li>
                                    @endif

                                    {{-- Botón Siguiente --}}
                                    @if($facturas->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $facturas->nextPageUrl() }}" aria-label="Siguiente">
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
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Inicializar tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection

