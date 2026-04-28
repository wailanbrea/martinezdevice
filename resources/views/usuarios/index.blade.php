@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@push('css')
<style>
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
    @include('layouts.navbars.auth.topnav', ['title' => 'Gestión de Usuarios'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-white mb-0">Gestión de Usuarios</h2>
                    <p class="text-white text-sm opacity-8">Administre los usuarios del sistema</p>
                </div>
                <a href="{{ route('usuarios.create') }}" class="btn btn-cta">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
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

        @if(session('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
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
                        <form method="GET" action="{{ route('usuarios.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-10 mb-3">
                                    <label class="form-label">Buscar Usuario</label>
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Nombre de usuario, email, nombre..." 
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

        <!-- Tabla de Usuarios -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Lista de Usuarios</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="d-md-none px-3 pt-3">
                            @forelse($usuarios as $usuario)
                            <div class="border rounded-3 p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <h6 class="mb-1">{{ $usuario->firstname }} {{ $usuario->lastname }}</h6>
                                        <p class="text-xs text-secondary mb-1">@{{ $usuario->username }}</p>
                                        <p class="text-xs text-secondary mb-2">{{ $usuario->email }}</p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('usuarios.show', $usuario) }}" class="text-secondary" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('usuarios.edit', $usuario) }}" class="text-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-1 mb-2">
                                    @foreach($usuario->roles as $rol)
                                        <span class="badge badge-sm bg-gradient-{{ $rol->slug == 'administrador' ? 'danger' : ($rol->slug == 'tecnico' ? 'info' : 'secondary') }}">
                                            {{ $rol->nombre }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="small text-secondary mb-2">Registrado: {{ $usuario->created_at->format('d/m/Y') }}</div>
                                @if($usuario->id !== auth()->id())
                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        Eliminar usuario
                                    </button>
                                </form>
                                @endif
                            </div>
                            @empty
                            <p class="text-sm text-secondary mb-0 text-center py-4">No se encontraron usuarios</p>
                            @endforelse
                        </div>
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0 d-none d-md-table">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Roles</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha Registro</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usuarios as $usuario)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $usuario->firstname }} {{ $usuario->lastname }}</h6>
                                                    <p class="text-xs text-secondary mb-0">@{{ $usuario->username }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">{{ $usuario->email }}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($usuario->roles as $rol)
                                                    <span class="badge badge-sm bg-gradient-{{ $rol->slug == 'administrador' ? 'danger' : ($rol->slug == 'tecnico' ? 'info' : 'secondary') }}">
                                                        {{ $rol->nombre }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-sm">{{ $usuario->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('usuarios.show', $usuario) }}" class="text-secondary font-weight-bold text-xs me-2" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('usuarios.edit', $usuario) }}" class="text-secondary font-weight-bold text-xs me-2" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($usuario->id !== auth()->id())
                                                <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger font-weight-bold text-xs m-0 p-0" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <p class="text-sm text-secondary mb-0">No se encontraron usuarios</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($usuarios->hasPages())
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="text-sm text-secondary">
                                Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} resultados
                            </div>
                            <nav aria-label="Navegación de páginas">
                                <ul class="pagination pagination-sm mb-0">
                                    @if($usuarios->onFirstPage())
                                        <li class="page-item disabled">
                                            <span class="page-link border-0"><i class="fas fa-chevron-left fa-xs"></i></span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $usuarios->previousPageUrl() }}"><i class="fas fa-chevron-left fa-xs"></i></a>
                                        </li>
                                    @endif

                                    @php
                                        $currentPage = $usuarios->currentPage();
                                        $lastPage = $usuarios->lastPage();
                                        $startPage = max(1, $currentPage - 2);
                                        $endPage = min($lastPage, $currentPage + 2);
                                    @endphp

                                    @if($startPage > 1)
                                        <li class="page-item"><a class="page-link border-0" href="{{ $usuarios->url(1) }}">1</a></li>
                                        @if($startPage > 2)
                                            <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                                        @endif
                                    @endif

                                    @for($page = $startPage; $page <= $endPage; $page++)
                                        @if($page == $currentPage)
                                            <li class="page-item active"><span class="page-link border-0">{{ $page }}</span></li>
                                        @else
                                            <li class="page-item"><a class="page-link border-0" href="{{ $usuarios->url($page) }}">{{ $page }}</a></li>
                                        @endif
                                    @endfor

                                    @if($endPage < $lastPage)
                                        @if($endPage < $lastPage - 1)
                                            <li class="page-item disabled"><span class="page-link border-0">...</span></li>
                                        @endif
                                        <li class="page-item"><a class="page-link border-0" href="{{ $usuarios->url($lastPage) }}">{{ $lastPage }}</a></li>
                                    @endif

                                    @if($usuarios->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link border-0" href="{{ $usuarios->nextPageUrl() }}"><i class="fas fa-chevron-right fa-xs"></i></a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <span class="page-link border-0"><i class="fas fa-chevron-right fa-xs"></i></span>
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
