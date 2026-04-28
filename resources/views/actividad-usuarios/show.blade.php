@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Actividad de ' . $usuario->firstname . ' ' . $usuario->lastname])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white mb-0">{{ $usuario->firstname }} {{ $usuario->lastname }}</h2>
                        <p class="text-white text-sm opacity-8 mb-0">{{ $usuario->email }}</p>
                        <div class="mt-2">
                            @foreach($usuario->roles as $rol)
                                <span class="badge bg-gradient-{{ $rol->slug == 'administrador' ? 'danger' : ($rol->slug == 'tecnico' ? 'primary' : 'info') }}">
                                    {{ ucfirst($rol->nombre) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('actividad-usuarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtro de Fechas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('actividad-usuarios.show', $usuario->id) }}">
                            <div class="row align-items-end">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Fecha Inicio</label>
                                    <input type="date" name="fecha_inicio" class="form-control" 
                                           value="{{ $fechaInicio }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Fecha Fin</label>
                                    <input type="date" name="fecha_fin" class="form-control" 
                                           value="{{ $fechaFin }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="submit" class="btn btn-primary w-100 mb-0">
                                        <i class="fas fa-filter me-1"></i>Aplicar Filtro
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Resumen -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Rep. Creadas</p>
                                    <h5 class="font-weight-bolder mb-0 text-info">{{ $stats['reparaciones_creadas'] }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="ni ni-fat-add text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-success">Rep. Completadas</p>
                                    <h5 class="font-weight-bolder mb-0 text-success">{{ $stats['reparaciones_completadas'] }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-check-bold text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-danger">Comisiones</p>
                                    <h5 class="font-weight-bolder mb-0 text-danger">${{ number_format($stats['total_comisiones'], 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="ni ni-money-coins text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-primary">Ingresos Gen.</p>
                                    <h5 class="font-weight-bolder mb-0 text-primary">${{ number_format($stats['ingresos_generados'], 2) }}</h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow-primary text-center rounded-circle">
                                    <i class="ni ni-chart-bar-32 text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs para diferentes secciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <ul class="nav nav-tabs" id="activityTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="comisiones-tab" data-bs-toggle="tab" data-bs-target="#comisiones" type="button" role="tab">
                                    <i class="fas fa-dollar-sign me-2"></i>Comisiones ({{ $comisionesDetalladas->total() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completadas-tab" data-bs-toggle="tab" data-bs-target="#completadas" type="button" role="tab">
                                    <i class="fas fa-check-circle me-2"></i>Completadas ({{ $reparacionesCompletadas->total() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="creadas-tab" data-bs-toggle="tab" data-bs-target="#creadas" type="button" role="tab">
                                    <i class="fas fa-plus-circle me-2"></i>Creadas ({{ $reparacionesCreadas->total() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="asignadas-tab" data-bs-toggle="tab" data-bs-target="#asignadas" type="button" role="tab">
                                    <i class="fas fa-tasks me-2"></i>Asignadas ({{ $reparacionesAsignadas->total() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="estados-tab" data-bs-toggle="tab" data-bs-target="#estados" type="button" role="tab">
                                    <i class="fas fa-exchange-alt me-2"></i>Cambios Estado ({{ $cambiosEstado->total() }})
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="notas-tab" data-bs-toggle="tab" data-bs-target="#notas" type="button" role="tab">
                                    <i class="fas fa-sticky-note me-2"></i>Notas ({{ $notasAgregadas->total() }})
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="activityTabsContent">
                            <!-- Tab Comisiones -->
                            <div class="tab-pane fade show active" id="comisiones" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Código</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cliente</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Fecha</th>
                                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Ingreso</th>
                                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">%</th>
                                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Comisión</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($comisionesDetalladas->items() as $rep)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('reparaciones.show', $rep->id) }}" class="text-primary text-decoration-none">
                                                        <strong>{{ $rep->codigo_reparacion }}</strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    <p class="text-sm mb-0">{{ $rep->equipo->cliente->nombre ?? 'N/A' }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs">{{ $rep->fecha_finalizacion ? $rep->fecha_finalizacion->format('d/m/Y') : 'N/A' }}</span>
                                                </td>
                                                <td class="align-middle text-end">
                                                    <span class="text-sm text-success">${{ number_format($rep->precio_cotizado ?? $rep->total_estimado ?? 0, 2) }}</span>
                                                </td>
                                                <td class="align-middle text-end">
                                                    <span class="text-sm">{{ number_format($rep->porcentaje_comision, 1) }}%</span>
                                                </td>
                                                <td class="align-middle text-end">
                                                    <strong class="text-danger">${{ number_format($rep->monto_comision, 2) }}</strong>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <p class="text-sm text-secondary mb-0">No hay comisiones en el período seleccionado</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                        @if($comisionesDetalladas->count() > 0)
                                        <tfoot>
                                            <tr class="border-top">
                                                <td colspan="5" class="text-end">
                                                    <strong>TOTAL COMISIONES:</strong>
                                                </td>
                                                <td class="text-end">
                                                    <strong class="text-danger">${{ number_format($stats['total_comisiones'], 2) }}</strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $comisionesDetalladas->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->links() }}
                                </div>
                            </div>

                            <!-- Tab Completadas -->
                            <div class="tab-pane fade" id="completadas" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Código</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cliente</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Fecha</th>
                                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Ingreso</th>
                                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Comisión</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reparacionesCompletadas->items() as $rep)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('reparaciones.show', $rep->id) }}" class="text-primary text-decoration-none">
                                                        <strong>{{ $rep->codigo_reparacion }}</strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    <p class="text-sm mb-0">{{ $rep->equipo->cliente->nombre ?? 'N/A' }}</p>
                                                    <p class="text-xs text-secondary mb-0">{{ $rep->tipo_servicio }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs">{{ $rep->fecha_finalizacion ? $rep->fecha_finalizacion->format('d/m/Y') : 'N/A' }}</span>
                                                </td>
                                                <td class="align-middle text-end">
                                                    <span class="text-sm font-weight-bold text-success">${{ number_format($rep->precio_cotizado ?? $rep->total_estimado ?? 0, 2) }}</span>
                                                </td>
                                                <td class="align-middle text-end">
                                                    @if($rep->monto_comision)
                                                        <span class="text-sm font-weight-bold text-danger">${{ number_format($rep->monto_comision, 2) }}</span>
                                                    @else
                                                        <span class="text-xs text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <p class="text-sm text-secondary mb-0">No hay reparaciones completadas en el período</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $reparacionesCompletadas->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->links() }}
                                </div>
                            </div>

                            <!-- Tab Creadas -->
                            <div class="tab-pane fade" id="creadas" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Código</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cliente</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Fecha</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Estado</th>
                                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reparacionesCreadas->items() as $rep)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('reparaciones.show', $rep->id) }}" class="text-primary text-decoration-none">
                                                        <strong>{{ $rep->codigo_reparacion }}</strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    <p class="text-sm mb-0">{{ $rep->equipo->cliente->nombre ?? 'N/A' }}</p>
                                                    <p class="text-xs text-secondary mb-0">{{ $rep->tipo_servicio }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs">{{ $rep->created_at->format('d/m/Y H:i') }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <x-badge-estado :estado="$rep->estado" size="sm" />
                                                </td>
                                                <td class="align-middle text-end">
                                                    <span class="text-sm font-weight-bold">${{ number_format($rep->total_estimado ?? 0, 2) }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <p class="text-sm text-secondary mb-0">No hay reparaciones creadas en el período</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $reparacionesCreadas->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->links() }}
                                </div>
                            </div>

                            <!-- Tab Asignadas -->
                            <div class="tab-pane fade" id="asignadas" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Código</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Cliente</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Fecha Ingreso</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Estado</th>
                                                <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($reparacionesAsignadas->items() as $rep)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('reparaciones.show', $rep->id) }}" class="text-primary text-decoration-none">
                                                        <strong>{{ $rep->codigo_reparacion }}</strong>
                                                    </a>
                                                </td>
                                                <td>
                                                    <p class="text-sm mb-0">{{ $rep->equipo->cliente->nombre ?? 'N/A' }}</p>
                                                    <p class="text-xs text-secondary mb-0">{{ $rep->tipo_servicio }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs">{{ $rep->fecha_ingreso ? $rep->fecha_ingreso->format('d/m/Y') : 'N/A' }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <x-badge-estado :estado="$rep->estado" size="sm" />
                                                </td>
                                                <td class="align-middle text-end">
                                                    <span class="text-sm font-weight-bold">${{ number_format($rep->total_estimado ?? 0, 2) }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <p class="text-sm text-secondary mb-0">No hay reparaciones asignadas actualmente</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $reparacionesAsignadas->links() }}
                                </div>
                            </div>

                            <!-- Tab Cambios de Estado -->
                            <div class="tab-pane fade" id="estados" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Reparación</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Estado</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Comentario</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($cambiosEstado->items() as $cambio)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('reparaciones.show', $cambio->reparacion_id) }}" class="text-primary text-decoration-none">
                                                        <strong>{{ $cambio->reparacion->codigo_reparacion ?? 'N/A' }}</strong>
                                                    </a>
                                                    <p class="text-xs text-secondary mb-0">{{ $cambio->reparacion->equipo->cliente->nombre ?? 'N/A' }}</p>
                                                </td>
                                                <td>
                                                    <x-badge-estado :estado="$cambio->estado" size="sm" />
                                                </td>
                                                <td>
                                                    <p class="text-sm mb-0">{{ \Illuminate\Support\Str::limit($cambio->comentario ?? 'Sin comentario', 50) }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs">{{ $cambio->created_at->format('d/m/Y H:i') }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4">
                                                    <p class="text-sm text-secondary mb-0">No hay cambios de estado en el período</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $cambiosEstado->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->links() }}
                                </div>
                            </div>

                            <!-- Tab Notas -->
                            <div class="tab-pane fade" id="notas" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Reparación</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Nota</th>
                                                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Fecha</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($notasAgregadas->items() as $nota)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('reparaciones.show', $nota->reparacion_id) }}" class="text-primary text-decoration-none">
                                                        <strong>{{ $nota->reparacion->codigo_reparacion ?? 'N/A' }}</strong>
                                                    </a>
                                                    <p class="text-xs text-secondary mb-0">{{ $nota->reparacion->equipo->cliente->nombre ?? 'N/A' }}</p>
                                                </td>
                                                <td>
                                                    <p class="text-sm mb-0">{{ \Illuminate\Support\Str::limit($nota->nota, 100) }}</p>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="text-xs">{{ $nota->created_at->format('d/m/Y H:i') }}</span>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-4">
                                                    <p class="text-sm text-secondary mb-0">No hay notas agregadas en el período</p>
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $notasAgregadas->appends(['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin])->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection

@push('css')
<style>
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
    .border-left-danger {
        border-left: 4px solid #dc3545 !important;
    }
    .border-left-primary {
        border-left: 4px solid #5e72e4 !important;
    }
    .border-left-info {
        border-left: 4px solid #11cdef !important;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #5e72e4;
        font-weight: bold;
    }
</style>
@endpush

