@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Equipos'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 mb-4">
                <h2 class="text-white mb-0">Gestión de Equipos</h2>
                <p class="text-white text-sm opacity-8">Todos los equipos registrados en el sistema</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('equipos.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Buscar</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Marca, modelo, serie, cliente..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Tipo</label>
                                    <select name="tipo" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="PC de Escritorio" {{ request('tipo') == 'PC de Escritorio' ? 'selected' : '' }}>PC de Escritorio</option>
                                        <option value="Laptop" {{ request('tipo') == 'Laptop' ? 'selected' : '' }}>Laptop</option>
                                        <option value="Tarjeta Gráfica (GPU)" {{ request('tipo') == 'Tarjeta Gráfica (GPU)' ? 'selected' : '' }}>GPU</option>
                                        <option value="Consola de Videojuegos" {{ request('tipo') == 'Consola de Videojuegos' ? 'selected' : '' }}>Consola</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">Estado</label>
                                    <select name="estado" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="recibido">Recibido</option>
                                        <option value="diagnostico">Diagnóstico</option>
                                        <option value="reparacion">Reparación</option>
                                        <option value="listo">Listo</option>
                                        <option value="entregado">Entregado</option>
                                    </select>
                                </div>
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

        <!-- Tabla de Equipos -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Lista de Equipos</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Equipo</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">N/S</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Reparaciones</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($equipos as $equipo)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $equipo->tipo }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $equipo->marca }} {{ $equipo->modelo }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">{{ $equipo->cliente->nombre }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $equipo->cliente->telefono }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">{{ $equipo->numero_serie ?? 'N/A' }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                $estadoClasses = [
                                                    'recibido' => 'bg-gradient-secondary',
                                                    'diagnostico' => 'bg-gradient-info',
                                                    'reparacion' => 'bg-gradient-warning',
                                                    'listo' => 'bg-gradient-success',
                                                    'entregado' => 'bg-gradient-dark',
                                                    'garantia' => 'bg-gradient-primary',
                                                ];
                                            @endphp
                                            <span class="badge badge-sm {{ $estadoClasses[$equipo->estado] ?? 'bg-gradient-secondary' }}">
                                                {{ ucfirst($equipo->estado) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-info">{{ $equipo->reparaciones_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('equipos.show', $equipo) }}" class="text-secondary font-weight-bold text-xs me-2" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('equipos.historial', $equipo) }}" class="text-primary font-weight-bold text-xs me-2" title="Ver historial completo">
                                                <i class="fas fa-history"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-sm text-secondary mb-0">No se encontraron equipos</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($equipos->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <!-- Información de resultados -->
                            <div class="text-sm text-secondary">
                                Mostrando {{ $equipos->firstItem() }} a {{ $equipos->lastItem() }} de {{ $equipos->total() }} resultados
                            </div>
                            
                            <!-- Navegación de páginas -->
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Botón Anterior --}}
                                    @if($equipos->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link border-0">
                                                <i class="fas fa-chevron-left fa-xs"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $equipos->previousPageUrl() }}" aria-label="Anterior">
                                                <i class="fas fa-chevron-left fa-xs"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Números de página --}}
                                    @php
                                        $currentPage = $equipos->currentPage();
                                        $lastPage = $equipos->lastPage();
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($lastPage, $currentPage + 2);
                                    @endphp

                                    @if($startPage > 1)
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $equipos->url(1) }}">1</a>
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
                                                <a class="page-link border-0" href="{{ $equipos->url($page) }}">{{ $page }}</a>
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
                                            <a class="page-link border-0" href="{{ $equipos->url($lastPage) }}">{{ $lastPage }}</a>
                                        </li>
                                    @endif

                                    {{-- Botón Siguiente --}}
                                    @if($equipos->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $equipos->nextPageUrl() }}" aria-label="Siguiente">
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

