@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Reparaciones'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">Listado de Reparaciones</h2>
                    <p class="text-white text-sm opacity-8">Gestión completa de órdenes de reparación</p>
                </div>
                <a href="{{ route('reparaciones.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nueva Reparación
                </a>
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

        <!-- Filtros y Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('reparaciones.index') }}">
                            <div class="row align-items-end">
                                <!-- Búsqueda -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Buscar</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="ID, cliente, equipo..." 
                                           value="{{ request('search') }}">
                                </div>

                                <!-- Filtro por Estado -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="">Todos los estados</option>
                                        <option value="Recibido" {{ request('estado') == 'Recibido' ? 'selected' : '' }}>Recibido</option>
                                        <option value="En Diagnóstico" {{ request('estado') == 'En Diagnóstico' ? 'selected' : '' }}>En Diagnóstico</option>
                                        <option value="Esperando Pieza" {{ request('estado') == 'Esperando Pieza' ? 'selected' : '' }}>Esperando Pieza</option>
                                        <option value="En Proceso" {{ request('estado') == 'En Proceso' ? 'selected' : '' }}>En Proceso</option>
                                        <option value="Finalizado" {{ request('estado') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                    </select>
                                </div>

                                <!-- Filtro por Técnico -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Técnico Asignado</label>
                                    <select name="tecnico_id" class="form-select">
                                        <option value="">Todos los técnicos</option>
                                        @foreach($tecnicos as $tecnico)
                                            <option value="{{ $tecnico->id }}" 
                                                {{ request('tecnico_id') == $tecnico->id ? 'selected' : '' }}>
                                                {{ $tecnico->firstname }} {{ $tecnico->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
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

        <!-- Tabla de Reparaciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Reparaciones</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Equipo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Técnico</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha Ingreso</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($reparaciones as $reparacion)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $reparacion->codigo_reparacion }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->cliente->nombre }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $reparacion->equipo->cliente->telefono }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $reparacion->equipo->tipo }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $reparacion->equipo->marca }} {{ $reparacion->equipo->modelo }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">
                                                {{ $reparacion->tecnico ? $reparacion->tecnico->firstname . ' ' . $reparacion->tecnico->lastname : 'No asignado' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-xs">{{ $reparacion->fecha_ingreso->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                $badgeClass = match($reparacion->estado) {
                                                    'Recibido' => 'bg-gradient-secondary',
                                                    'En Diagnóstico' => 'bg-gradient-info',
                                                    'Esperando Pieza' => 'bg-gradient-warning',
                                                    'En Proceso' => 'bg-gradient-primary',
                                                    'Finalizado' => 'bg-gradient-success',
                                                    'Cancelado' => 'bg-gradient-danger',
                                                    default => 'bg-gradient-secondary',
                                                };
                                            @endphp
                                            <span class="badge badge-sm {{ $badgeClass }}">{{ $reparacion->estado }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('reparaciones.show', $reparacion) }}" class="text-secondary font-weight-bold text-xs me-2" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('reparaciones.etiqueta-entrada', $reparacion) }}" class="text-info font-weight-bold text-xs me-2" title="Imprimir etiqueta">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="{{ route('reparaciones.edit', $reparacion) }}" class="text-secondary font-weight-bold text-xs me-2" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('reparaciones.destroy', $reparacion) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('¿Estás seguro de eliminar esta reparación?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs m-0 p-0" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-sm text-secondary mb-0">No se encontraron reparaciones</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($reparaciones->hasPages())
                    <div class="card-footer">
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
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

