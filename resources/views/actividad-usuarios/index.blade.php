@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Actividad de Usuarios'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white mb-0">Actividad de Usuarios</h2>
                <p class="text-white text-sm opacity-8">Seguimiento completo de acciones y comisiones de cada usuario</p>
            </div>
        </div>

        <!-- Búsqueda -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('actividad-usuarios.index') }}">
                            <div class="row align-items-end">
                                <div class="col-md-10 mb-3">
                                    <label class="form-label">Buscar Usuario</label>
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Nombre, apellido o email..."
                                           value="{{ $search }}">
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

        <!-- Lista de Usuarios -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-users me-2"></i>Usuarios del Sistema</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Usuario</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rep. Creadas</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rep. Completadas</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Rep. Asignadas</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comisiones</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ingresos Gen.</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usuarios as $usuario)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $usuario->firstname }} {{ $usuario->lastname }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $usuario->email }}</p>
                                                    <div class="mt-1">
                                                        @foreach($usuario->roles as $rol)
                                                            <span class="badge badge-sm bg-gradient-{{ $rol->slug == 'administrador' ? 'danger' : ($rol->slug == 'tecnico' ? 'primary' : 'info') }}">
                                                                {{ ucfirst($rol->nombre) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-info">{{ $usuario->stats['reparaciones_creadas'] }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-success">{{ $usuario->stats['reparaciones_completadas'] }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-warning">{{ $usuario->stats['reparaciones_asignadas'] }}</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-danger">
                                                ${{ number_format($usuario->stats['total_comisiones'], 2) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-success">
                                                ${{ number_format($usuario->stats['ingresos_generados'], 2) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('actividad-usuarios.show', $usuario->id) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye me-1"></i>Ver Detalle
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-sm text-secondary mb-0">No se encontraron usuarios</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Paginación -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-center">
                                {{ $usuarios->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

