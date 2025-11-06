<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ContabilidadController extends Controller
{
    public function ingresos(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());

        $ingresos = Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['factura.cliente', 'usuario'])
            ->get();

        $total = $ingresos->sum('monto');
        $porTipoPago = $ingresos->groupBy('tipo_pago')->map->sum('monto');

        return response()->json([
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin,
            ],
            'total' => $total,
            'por_tipo_pago' => $porTipoPago,
            'ingresos' => $ingresos,
        ]);
    }

    public function ingresosDiarios()
    {
        $ingresos = Pago::select(
                DB::raw('DATE(fecha) as fecha'),
                DB::raw('SUM(monto) as total')
            )
            ->where('fecha', '>=', now()->subDays(30))
            ->groupBy(DB::raw('DATE(fecha)'))
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($ingresos);
    }

    public function ingresosSemanales()
    {
        $ingresos = Pago::select(
                DB::raw('YEARWEEK(fecha) as semana'),
                DB::raw('SUM(monto) as total')
            )
            ->where('fecha', '>=', now()->subWeeks(12))
            ->groupBy(DB::raw('YEARWEEK(fecha)'))
            ->orderBy('semana', 'desc')
            ->get();

        return response()->json($ingresos);
    }

    public function ingresosMensuales()
    {
        $ingresos = Pago::select(
                DB::raw('YEAR(fecha) as año'),
                DB::raw('MONTH(fecha) as mes'),
                DB::raw('SUM(monto) as total')
            )
            ->where('fecha', '>=', now()->subMonths(12))
            ->groupBy(DB::raw('YEAR(fecha)'), DB::raw('MONTH(fecha)'))
            ->orderBy('año', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return response()->json($ingresos);
    }

    public function exportarCSV(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());

        $ingresos = Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['factura.cliente', 'usuario'])
            ->get();

        $filename = 'ingresos_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($ingresos) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Fecha', 'Factura', 'Cliente', 'Monto', 'Tipo Pago', 'Usuario']);

            foreach ($ingresos as $pago) {
                fputcsv($file, [
                    $pago->fecha->format('Y-m-d'),
                    $pago->factura->numero_factura,
                    $pago->factura->cliente->nombre,
                    $pago->monto,
                    $pago->tipo_pago,
                    $pago->usuario->name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportarPDF(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());

        $ingresos = Pago::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['factura.cliente', 'usuario'])
            ->get();

        $total = $ingresos->sum('monto');
        $porTipoPago = $ingresos->groupBy('tipo_pago')->map->sum('monto');

        $pdf = Pdf::loadView('reportes.ingresos', compact('ingresos', 'total', 'porTipoPago', 'fechaInicio', 'fechaFin'));
        return $pdf->download('reporte_ingresos_' . now()->format('Y-m-d') . '.pdf');
    }
}
