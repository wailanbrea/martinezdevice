<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContabilidadController extends Controller
{
    public function index()
    {
        $ingresos_mes = Factura::whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->sum('total');
        
        $ingresos_hoy = Factura::whereDate('fecha_emision', now()->toDateString())
            ->sum('total');
        
        $facturas_pendientes = Factura::whereDoesntHave('pagos')->count();
        
        $facturas_mes = Factura::whereMonth('fecha_emision', now()->month)
            ->whereYear('fecha_emision', now()->year)
            ->count();

        return view('contabilidad.index', compact(
            'ingresos_mes',
            'ingresos_hoy',
            'facturas_pendientes',
            'facturas_mes'
        ));
    }
}
