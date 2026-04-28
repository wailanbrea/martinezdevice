@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

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
    .border-left-warning {
        border-left: 4px solid #fb6340 !important;
    }
</style>
@endpush

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Contabilidad'])
    <div class="container-fluid py-4">
        <!-- Page Heading -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white mb-0">Módulo de Contabilidad</h2>
                <p class="text-white text-sm opacity-8">Control de ingresos, gastos (comisiones) y reportes financieros</p>
            </div>
        </div>

        <!-- Filtro de Fechas -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('contabilidad.index') }}">
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
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary mb-0">
                                            <i class="fas fa-filter me-1"></i>Aplicar Filtro
                                        </button>
                                        <a href="{{ route('contabilidad.index') }}" class="btn btn-outline-secondary mb-0">
                                            <i class="fas fa-times-circle me-1"></i>Quitar Filtro
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen Financiero Principal -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-success">Ingresos Totales</p>
                                    <h5 class="font-weight-bolder mb-0 text-success">
                                        ${{ number_format($ingresosTotales, 2) }}
                                    </h5>
                                    <p class="mb-0 text-xs">
                                        <span class="text-muted">Facturas: ${{ number_format($ingresosFacturas, 2) }}</span><br>
                                        <span class="text-muted">Estimados: ${{ number_format($ingresosEstimados, 2) }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow-success text-center rounded-circle">
                                    <i class="ni ni-money-coins text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-danger">Comisiones Pagadas</p>
                                    <h5 class="font-weight-bolder mb-0 text-danger">
                                        ${{ number_format($totalComisiones, 2) }}
                                    </h5>
                                    <p class="mb-0 text-xs">
                                        <span class="text-muted">{{ $comisionesPorTecnico->count() }} técnicos</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                    <i class="ni ni-single-02 text-lg opacity-10"></i>
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
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-primary">Utilidad Neta</p>
                                    <h5 class="font-weight-bolder mb-0 text-primary">
                                        ${{ number_format($utilidadNeta, 2) }}
                                    </h5>
                                    <p class="mb-0 text-xs">
                                        <span class="text-muted">{{ $utilidadNeta > 0 ? number_format(($utilidadNeta / max($ingresosTotales, 1)) * 100, 1) : '0.0' }}% margen</span>
                                    </p>
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

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <p class="text-sm mb-0 text-uppercase font-weight-bold text-info">Reparaciones</p>
                                    <h5 class="font-weight-bolder mb-0 text-info">
                                        {{ $reparacionesCompletadas }}
                                    </h5>
                                    <p class="mb-0 text-xs">
                                        <span class="text-muted">{{ $facturasEmitidas }} facturas</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow-info text-center rounded-circle">
                                    <i class="ni ni-check-bold text-lg opacity-10"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Comisiones por Técnico -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-users me-2 text-danger"></i>Comisiones por Técnico</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Técnico</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Trabajos</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">% Prom.</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Comisión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($comisionesPorTecnico as $item)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">
                                                {{ $item->tecnico ? $item->tecnico->firstname . ' ' . $item->tecnico->lastname : 'N/A' }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-info">{{ $item->cantidad_trabajos }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs">{{ number_format($item->porcentaje_promedio, 1) }}%</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-danger">${{ number_format($item->total_comision, 2) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-sm text-secondary py-3">
                                            No hay comisiones registradas en el período seleccionado
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($comisionesPorTecnico->count() > 0)
                                <tfoot>
                                    <tr class="border-top">
                                        <td class="text-end" colspan="3">
                                            <strong>TOTAL COMISIONES:</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-danger">${{ number_format($totalComisiones, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingresos vs Comisiones por Técnico -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-chart-line me-2 text-primary"></i>Ingresos vs Comisiones por Técnico</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Técnico</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Ingresos</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Comisión</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Utilidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ingresosPorTecnico as $item)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">
                                                {{ $item->tecnico ? $item->tecnico->firstname . ' ' . $item->tecnico->lastname : 'N/A' }}
                                            </p>
                                            <p class="text-xs text-secondary mb-0">{{ $item->cantidad }} trabajos</p>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-success">${{ number_format($item->total_ingresos ?? 0, 2) }}</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-danger">${{ number_format($item->total_comision ?? 0, 2) }}</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-primary">${{ number_format($item->utilidad_tecnico ?? 0, 2) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-sm text-secondary py-3">
                                            No hay datos en el período seleccionado
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Ingresos por Tipo de Servicio -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-tags me-2 text-warning"></i>Ingresos por Tipo de Servicio</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tipo</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cantidad</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Ingresos</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Comisiones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ingresosPorTipoServicio as $item)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0 text-capitalize">{{ $item->tipo }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-primary">{{ $item->cantidad }}</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-success">${{ number_format($item->total, 2) }}</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-danger">${{ number_format($item->total_comisiones ?? 0, 2) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-sm text-secondary py-3">
                                            No hay datos en el período seleccionado
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingresos por Tipo de Equipo -->
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header pb-0">
                        <h6><i class="fas fa-laptop me-2 text-info"></i>Ingresos por Tipo de Equipo</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder">Tipo</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder">Cantidad</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ingresosPorTipo as $item)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">{{ $item->tipo }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-sm bg-gradient-primary">{{ $item->cantidad }}</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold">${{ number_format($item->total, 2) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-sm text-secondary py-3">
                                            No hay datos en el período seleccionado
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Todas las Reparaciones Completadas con Comisiones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6><i class="fas fa-list me-2"></i>Todas las Reparaciones Completadas ({{ $ultimasReparaciones->total() }})</h6>
                        <span class="badge bg-gradient-primary">{{ $ultimasReparaciones->currentPage() }} de {{ $ultimasReparaciones->lastPage() }}</span>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Técnico</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fecha</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ingreso</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Comisión</th>
                                        <th class="text-end text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Utilidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ultimasReparaciones->items() as $rep)
                                    <tr>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0 px-3">
                                                <a href="{{ route('reparaciones.show', $rep->id) }}" class="text-primary text-decoration-none">
                                                    {{ $rep->codigo_reparacion }}
                                                </a>
                                            </p>
                                        </td>
                                        <td>
                                            <p class="text-sm mb-0">{{ $rep->equipo->cliente->nombre ?? 'N/A' }}</p>
                                            <p class="text-xs text-secondary mb-0">{{ $rep->tipo_servicio }}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs mb-0">
                                                {{ $rep->tecnicoCompleto ? $rep->tecnicoCompleto->firstname . ' ' . $rep->tecnicoCompleto->lastname : ($rep->tecnico ? $rep->tecnico->firstname . ' ' . $rep->tecnico->lastname : 'N/A') }}
                                            </p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-xs">{{ $rep->fecha_finalizacion ? $rep->fecha_finalizacion->format('d/m/Y') : 'N/A' }}</span>
                                        </td>
                                        <td class="align-middle text-end">
                                            <span class="text-sm font-weight-bold text-success">
                                                ${{ number_format($rep->precio_cotizado ?? $rep->total_estimado ?? 0, 2) }}
                                            </span>
                                        </td>
                                        <td class="align-middle text-end">
                                            @if($rep->monto_comision)
                                                <span class="text-sm font-weight-bold text-danger">
                                                    ${{ number_format($rep->monto_comision, 2) }}
                                                </span>
                                                <br>
                                                <small class="text-xs text-muted">({{ $rep->porcentaje_comision }}%)</small>
                                            @else
                                                <span class="text-xs text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-end">
                                            @php
                                                $ingreso = $rep->precio_cotizado ?? $rep->total_estimado ?? 0;
                                                $comision = $rep->monto_comision ?? 0;
                                                $utilidad = $ingreso - $comision;
                                            @endphp
                                            <span class="text-sm font-weight-bold text-primary">
                                                ${{ number_format($utilidad, 2) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-sm text-secondary mb-0">No hay reparaciones completadas en este período</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="border-top">
                                        <td colspan="4" class="text-end pe-3">
                                            <strong>TOTALES DEL PERÍODO:</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">${{ number_format($ingresosTotales, 2) }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-danger">${{ number_format($totalComisiones, 2) }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-primary">${{ number_format($utilidadNeta, 2) }}</strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- Paginación -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-center">
                                {{ $ultimasReparaciones->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.footers.auth.footer')
    </div>
@endsection
