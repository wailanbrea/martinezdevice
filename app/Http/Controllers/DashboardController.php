<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\EstadoReparacion;
use App\Models\Reparacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $estadosPendientes = ['Recibido', 'En Diagnóstico', 'Pendiente Revisión Admin', 'Esperando Aprobación', 'Aprobado', 'En Proceso'];
        $tipoGpu = 'Tarjeta Gráfica (GPU)';

        $stats = Cache::remember('dashboard.stats', 60, function () {
            $driver = \DB::connection()->getDriverName();

            if ($driver === 'mysql') {
                $estadoCounts = Reparacion::selectRaw("
                        SUM(CASE WHEN estado = 'En Proceso' THEN 1 ELSE 0 END) AS en_progreso,
                        SUM(CASE WHEN estado = 'Recibido' THEN 1 ELSE 0 END) AS pendientes_revision,
                        SUM(CASE WHEN estado IN ('Finalizado', 'Entregado') AND MONTH(fecha_finalizacion) = MONTH(CURRENT_DATE()) AND YEAR(fecha_finalizacion) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) AS completadas
                    ")
                    ->first();
            } else {
                $mesActual = Carbon::now()->startOfMonth();
                $estadoCounts = (object) [
                    'en_progreso' => Reparacion::where('estado', 'En Proceso')->count(),
                    'pendientes_revision' => Reparacion::where('estado', 'Recibido')->count(),
                    'completadas' => Reparacion::whereIn('estado', ['Finalizado', 'Entregado'])
                        ->whereNotNull('fecha_finalizacion')
                        ->where('fecha_finalizacion', '>=', $mesActual)
                        ->count(),
                ];
            }

            $garantiasVencidas = Reparacion::where('es_garantia', true)
                ->whereNotNull('fecha_vencimiento_garantia')
                ->whereDate('fecha_vencimiento_garantia', '<', Carbon::now())
                ->count();

            $garantiasPorVencer = Reparacion::where('es_garantia', true)
                ->whereNotNull('fecha_vencimiento_garantia')
                ->whereDate('fecha_vencimiento_garantia', '>=', Carbon::now())
                ->whereDate('fecha_vencimiento_garantia', '<=', Carbon::now()->addDays(7))
                ->count();

            $totalGarantias = Reparacion::where('es_garantia', true)->count();

            return [
                'en_progreso' => (int) ($estadoCounts->en_progreso ?? 0),
                'pendientes_revision' => (int) ($estadoCounts->pendientes_revision ?? 0),
                'completadas' => (int) ($estadoCounts->completadas ?? 0),
                'total_equipos' => Equipo::count(),
                'total_clientes' => Cliente::count(),
                'total_garantias' => $totalGarantias,
                'garantias_vencidas' => $garantiasVencidas,
                'garantias_por_vencer' => $garantiasPorVencer,
            ];
        });

        $actividadReciente = EstadoReparacion::with([
            'reparacion:id,codigo_reparacion,equipo_id',
            'reparacion.equipo:id,cliente_id,marca,modelo',
            'reparacion.equipo.cliente:id,nombre',
            'usuario:id,firstname,lastname',
        ])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $reparacionesPorMes = Cache::remember('dashboard.reparaciones_por_mes', 60, function () {
            $inicio = Carbon::now()->startOfMonth()->subMonths(5);
            $driver = \DB::connection()->getDriverName();

            if ($driver === 'mysql') {
                $resultados = Reparacion::selectRaw("
                        DATE_FORMAT(fecha_ingreso, '%Y-%m') as mes,
                        COUNT(*) as count
                    ")
                    ->whereNotNull('fecha_ingreso')
                    ->whereDate('fecha_ingreso', '>=', $inicio)
                    ->groupBy('mes')
                    ->orderBy('mes', 'asc')
                    ->pluck('count', 'mes')
                    ->toArray();
            } else {
                $resultados = Reparacion::selectRaw("
                        strftime('%Y-%m', fecha_ingreso) as mes,
                        COUNT(*) as count
                    ")
                    ->whereNotNull('fecha_ingreso')
                    ->whereDate('fecha_ingreso', '>=', $inicio)
                    ->groupBy('mes')
                    ->orderBy('mes', 'asc')
                    ->pluck('count', 'mes')
                    ->toArray();
            }

            $datos = [];
            for ($i = 5; $i >= 0; $i--) {
                $mes = Carbon::now()->subMonths($i);
                $clave = $mes->format('Y-m');

                $datos[] = [
                    'mes' => $mes->locale('es')->isoFormat('MMM'),
                    'count' => isset($resultados[$clave]) ? (int) $resultados[$clave] : 0,
                ];
            }

            return $datos;
        });

        $siguienteMantenimiento = Cache::remember('dashboard.siguiente_mantenimiento', 30, function () use ($estadosPendientes) {
            return Reparacion::with([
                'equipo:id,cliente_id,marca,modelo',
                'equipo.cliente:id,nombre',
            ])
                ->select('id', 'codigo_reparacion', 'equipo_id', 'fecha_ingreso', 'estado')
                ->where('tipo_servicio', 'mantenimiento')
                ->whereIn('estado', $estadosPendientes)
                ->orderBy('fecha_ingreso', 'asc')
                ->first();
        });

        $totalMantenimientos = Cache::remember('dashboard.total_mantenimientos', 30, function () use ($estadosPendientes) {
            return Reparacion::where('tipo_servicio', 'mantenimiento')
                ->whereIn('estado', $estadosPendientes)
                ->count();
        });

        $siguienteReparacion = Cache::remember('dashboard.siguiente_reparacion', 30, function () use ($estadosPendientes, $tipoGpu) {
            return Reparacion::with([
                'equipo:id,cliente_id,marca,modelo,tipo',
                'equipo.cliente:id,nombre',
            ])
                ->select('id', 'codigo_reparacion', 'equipo_id', 'fecha_ingreso', 'estado')
                ->where('tipo_servicio', 'reparacion')
                ->whereHas('equipo', function ($q) use ($tipoGpu) {
                    $q->where('tipo', '!=', $tipoGpu);
                })
                ->whereIn('estado', $estadosPendientes)
                ->orderBy('fecha_ingreso', 'asc')
                ->first();
        });

        $totalReparaciones = Cache::remember('dashboard.total_reparaciones', 30, function () use ($estadosPendientes, $tipoGpu) {
            return Reparacion::where('tipo_servicio', 'reparacion')
                ->whereHas('equipo', function ($q) use ($tipoGpu) {
                    $q->where('tipo', '!=', $tipoGpu);
                })
                ->whereIn('estado', $estadosPendientes)
                ->count();
        });

        $siguienteGPU = Cache::remember('dashboard.siguiente_gpu', 30, function () use ($estadosPendientes, $tipoGpu) {
            return Reparacion::with([
                'equipo:id,cliente_id,marca,modelo,tipo',
                'equipo.cliente:id,nombre',
            ])
                ->select('id', 'codigo_reparacion', 'equipo_id', 'fecha_ingreso', 'estado')
                ->where('tipo_servicio', 'reparacion')
                ->whereHas('equipo', function ($q) use ($tipoGpu) {
                    $q->where('tipo', $tipoGpu);
                })
                ->whereIn('estado', $estadosPendientes)
                ->orderBy('fecha_ingreso', 'asc')
                ->first();
        });

        $totalGPUs = Cache::remember('dashboard.total_gpus', 30, function () use ($estadosPendientes, $tipoGpu) {
            return Reparacion::where('tipo_servicio', 'reparacion')
                ->whereHas('equipo', function ($q) use ($tipoGpu) {
                    $q->where('tipo', $tipoGpu);
                })
                ->whereIn('estado', $estadosPendientes)
                ->count();
        });

        return view('dashboard', compact(
            'stats',
            'actividadReciente',
            'reparacionesPorMes',
            'siguienteMantenimiento',
            'siguienteReparacion',
            'siguienteGPU',
            'totalMantenimientos',
            'totalReparaciones',
            'totalGPUs'
        ));
    }

    public function check()
    {
        $payload = Cache::remember('dashboard.check', 5, function () {
            $estadosPendientes = ['Recibido', 'En Diagnóstico', 'Pendiente Revisión Admin', 'Esperando Aprobación', 'Aprobado', 'En Proceso'];
            $tipoGpu = 'Tarjeta Gráfica (GPU)';

            $siguienteMantenimiento = Reparacion::query()
                ->where('tipo_servicio', 'mantenimiento')
                ->whereIn('estado', $estadosPendientes)
                ->orderBy('fecha_ingreso', 'asc')
                ->value('id');

            $siguienteReparacion = Reparacion::query()
                ->join('equipos', 'equipos.id', '=', 'reparaciones.equipo_id')
                ->where('reparaciones.tipo_servicio', 'reparacion')
                ->where('equipos.tipo', '!=', $tipoGpu)
                ->whereIn('reparaciones.estado', $estadosPendientes)
                ->orderBy('reparaciones.fecha_ingreso', 'asc')
                ->value('reparaciones.id');

            $siguienteGPU = Reparacion::query()
                ->join('equipos', 'equipos.id', '=', 'reparaciones.equipo_id')
                ->where('reparaciones.tipo_servicio', 'reparacion')
                ->where('equipos.tipo', $tipoGpu)
                ->whereIn('reparaciones.estado', $estadosPendientes)
                ->orderBy('reparaciones.fecha_ingreso', 'asc')
                ->value('reparaciones.id');

            $totales = Reparacion::query()
                ->leftJoin('equipos', 'equipos.id', '=', 'reparaciones.equipo_id')
                ->whereIn('reparaciones.estado', $estadosPendientes)
                ->selectRaw(
                    "SUM(CASE WHEN reparaciones.tipo_servicio = 'mantenimiento' THEN 1 ELSE 0 END) AS total_mantenimientos,
                     SUM(CASE WHEN reparaciones.tipo_servicio = 'reparacion' AND equipos.tipo != ? THEN 1 ELSE 0 END) AS total_reparaciones,
                     SUM(CASE WHEN reparaciones.tipo_servicio = 'reparacion' AND equipos.tipo = ? THEN 1 ELSE 0 END) AS total_gpus",
                    [$tipoGpu, $tipoGpu]
                )
                ->first();

            return [
                'mantenimiento' => $siguienteMantenimiento,
                'reparacion' => $siguienteReparacion,
                'gpu' => $siguienteGPU,
                'total_mantenimientos' => (int) ($totales->total_mantenimientos ?? 0),
                'total_reparaciones' => (int) ($totales->total_reparaciones ?? 0),
                'total_gpus' => (int) ($totales->total_gpus ?? 0),
            ];
        });

        return response()->json($payload);
    }
}
