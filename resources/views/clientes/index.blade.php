@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Clientes'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">Gestión de Clientes</h2>
                    <p class="text-white text-sm opacity-8">Administre la base de clientes del taller</p>
                </div>
                <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Cliente
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('clientes.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-10 mb-3">
                                    <label class="form-label">Buscar Cliente</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Nombre, cédula, teléfono, email..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <button type="submit" class="btn btn-primary w-100 mb-0">
                                        <i class="fas fa-search me-1"></i>Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Clientes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Lista de Clientes</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cédula/RNC</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Contacto</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Equipos</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($clientes as $cliente)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $cliente->nombre }}</h6>
                                                    @if($cliente->email)
                                                        <p class="text-xs text-secondary mb-0">{{ $cliente->email }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">{{ $cliente->cedula_rnc ?? 'No proporcionado' }}</p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">
                                                <i class="fas fa-phone me-1 text-xs"></i>{{ $cliente->telefono }}
                                            </p>
                                            @if($cliente->direccion)
                                                <p class="text-xs text-secondary mb-0">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($cliente->direccion, 30) }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-info">{{ $cliente->equipos_count }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('clientes.show', $cliente) }}" class="text-secondary font-weight-bold text-xs me-2" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-secondary font-weight-bold text-xs me-2" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('¿Estás seguro de eliminar este cliente? Se eliminarán todos sus equipos.');">
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
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-sm text-secondary mb-0">No se encontraron clientes</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($clientes->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <!-- Información de resultados -->
                            <div class="text-sm text-secondary">
                                Mostrando {{ $clientes->firstItem() }} a {{ $clientes->lastItem() }} de {{ $clientes->total() }} resultados
                            </div>
                            
                            <!-- Navegación de páginas -->
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination pagination-sm mb-0">
                                    {{-- Botón Anterior --}}
                                    @if($clientes->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link border-0">
                                                <i class="fas fa-chevron-left fa-xs"></i>
                                            </span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $clientes->previousPageUrl() }}" aria-label="Anterior">
                                                <i class="fas fa-chevron-left fa-xs"></i>
                                            </a>
                                        </li>
                                    @endif

                                    {{-- Números de página --}}
                                    @php
                                        $currentPage = $clientes->currentPage();
                                        $lastPage = $clientes->lastPage();
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($lastPage, $currentPage + 2);
                                    @endphp

                                    @if($startPage > 1)
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $clientes->url(1) }}">1</a>
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
                                                <a class="page-link border-0" href="{{ $clientes->url($page) }}">{{ $page }}</a>
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
                                            <a class="page-link border-0" href="{{ $clientes->url($lastPage) }}">{{ $lastPage }}</a>
                                        </li>
                                    @endif

                                    {{-- Botón Siguiente --}}
                                    @if($clientes->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $clientes->nextPageUrl() }}" aria-label="Siguiente">
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
