<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Reparacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ContabilidadController extends Controller
{
    /**
     * Mostrar dashboard de contabilidad
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->fecha_inicio ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        $cacheKey = "contabilidad.resumen.{$fechaInicio}.{$fechaFin}";

        $resumen = Cache::remember($cacheKey, 120, function () use ($fechaInicio, $fechaFin) {
            $ingresosFacturas = Factura::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
                ->sum('total');

            $ingresosEstimados = Reparacion::whereIn('estado', ['Finalizado', 'Entregado'])
                ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                ->whereDoesntHave('factura')
                ->sum('total_estimado');

            $ingresosTotales = $ingresosFacturas + $ingresosEstimados;

            $totalComisiones = Reparacion::whereIn('estado', ['Finalizado', 'Entregado'])
                ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                ->whereNotNull('monto_comision')
                ->sum('monto_comision');

            $comisionesPorTecnico = Reparacion::select(
                    'tecnico_completo_id',
                    DB::raw('SUM(monto_comision) as total_comision'),
                    DB::raw('AVG(porcentaje_comision) as porcentaje_promedio'),
                    DB::raw('COUNT(*) as cantidad_trabajos')
                )
                ->whereIn('estado', ['Finalizado', 'Entregado'])
                ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                ->whereNotNull('tecnico_completo_id')
                ->whereNotNull('monto_comision')
                ->groupBy('tecnico_completo_id')
                ->get();

            $tecnicoIds = $comisionesPorTecnico->pluck('tecnico_completo_id')->unique();
            $tecnicos = \App\Models\User::whereIn('id', $tecnicoIds)->get()->keyBy('id');

            $comisionesPorTecnico = $comisionesPorTecnico->map(function ($item) use ($tecnicos) {
                $item->tecnico = $tecnicos->get($item->tecnico_completo_id);
                return $item;
            });

            $ingresosPorTecnico = Reparacion::select(
                    'tecnico_completo_id',
                    DB::raw('SUM(COALESCE(precio_cotizado, total_estimado)) as total_ingresos'),
                    DB::raw('SUM(monto_comision) as total_comision'),
                    DB::raw('COUNT(*) as cantidad')
                )
                ->whereIn('estado', ['Finalizado', 'Entregado'])
                ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                ->whereNotNull('tecnico_completo_id')
                ->groupBy('tecnico_completo_id')
                ->get()
                ->map(function ($item) use ($tecnicos) {
                    $item->tecnico = $tecnicos->get($item->tecnico_completo_id);
                    $item->utilidad_tecnico = ($item->total_ingresos ?? 0) - ($item->total_comision ?? 0);
                    return $item;
                });

            return [
                'ingresosFacturas' => $ingresosFacturas,
                'ingresosEstimados' => $ingresosEstimados,
                'ingresosTotales' => $ingresosTotales,
                'totalComisiones' => $totalComisiones,
                'utilidadNeta' => $ingresosTotales - $totalComisiones,
                'reparacionesCompletadas' => Reparacion::whereIn('estado', ['Finalizado', 'Entregado'])
                    ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                    ->count(),
                'facturasEmitidas' => Factura::whereBetween('fecha_emision', [$fechaInicio, $fechaFin])
                    ->count(),
                'comisionesPorTecnico' => $comisionesPorTecnico,
                'ingresosPorTecnico' => $ingresosPorTecnico,
                'ingresosPorTipoServicio' => Reparacion::select(
                        'tipo_servicio',
                        DB::raw('SUM(COALESCE(precio_cotizado, total_estimado)) as total'),
                        DB::raw('COUNT(*) as cantidad'),
                        DB::raw('SUM(monto_comision) as total_comisiones')
                    )
                    ->whereIn('estado', ['Finalizado', 'Entregado'])
                    ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                    ->groupBy('tipo_servicio')
                    ->get(),
                'ingresosPorTipo' => Reparacion::select(
                        'equipos.tipo',
                        DB::raw('SUM(COALESCE(reparaciones.precio_cotizado, reparaciones.total_estimado)) as total'),
                        DB::raw('COUNT(*) as cantidad')
                    )
                    ->join('equipos', 'reparaciones.equipo_id', '=', 'equipos.id')
                    ->whereIn('reparaciones.estado', ['Finalizado', 'Entregado'])
                    ->whereBetween('reparaciones.fecha_finalizacion', [$fechaInicio, $fechaFin])
                    ->groupBy('equipos.tipo')
                    ->get(),
                'ingresosPorDia' => Factura::select(
                        DB::raw('DATE(fecha_emision) as fecha'),
                        DB::raw('SUM(total) as total')
                    )
                    ->whereBetween('fecha_emision', [Carbon::now()->subDays(30), Carbon::now()])
                    ->groupBy('fecha')
                    ->orderBy('fecha', 'asc')
                    ->get(),
                'comisionesPorDia' => Reparacion::select(
                        DB::raw('DATE(fecha_finalizacion) as fecha'),
                        DB::raw('SUM(monto_comision) as total')
                    )
                    ->whereIn('estado', ['Finalizado', 'Entregado'])
                    ->whereBetween('fecha_finalizacion', [Carbon::now()->subDays(30), Carbon::now()])
                    ->whereNotNull('monto_comision')
                    ->groupBy('fecha')
                    ->orderBy('fecha', 'asc')
                    ->get(),
            ];
        });

        extract($resumen);

        $ultimasReparaciones = Reparacion::with([
                'equipo.cliente',
                'tecnicoCompleto',
                'recepcionista',
                'factura',
            ])
            ->whereIn('estado', ['Finalizado', 'Entregado'])
            ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_finalizacion', 'desc')
            ->paginate(25);

        return view('contabilidad.index', compact(
            'ingresosFacturas',
            'ingresosEstimados',
            'ingresosTotales',
            'totalComisiones',
            'utilidadNeta',
            'reparacionesCompletadas',
            'facturasEmitidas',
            'comisionesPorTecnico',
            'ingresosPorTecnico',
            'ingresosPorTipoServicio',
            'ingresosPorTipo',
            'ultimasReparaciones',
            'ingresosPorDia',
            'comisionesPorDia',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Ver reportes detallados
     */
    public function reportes(Request $request)
    {
        $tipo = $request->tipo ?? 'mensual';

        return view('contabilidad.reportes', compact('tipo'));
    }

    /**
     * Exportar datos
     */
    public function export($tipo)
    {
        return back()->with('info', 'Funcionalidad de exportacion en desarrollo');
    }
}
