<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Reparacion;

class DashboardController extends Controller
{
    public function index()
    {
        // Estadísticas de equipos
        $enReparacion = Equipo::where('estado', 'reparacion')->count();
        $entregados = Equipo::where('estado', 'entregado')->count();
        $enGarantia = Equipo::where('estado', 'garantia')->count();
        
        // Estadísticas de reparaciones
        $pendientes = Reparacion::where('estado', 'pendiente')->count();
        
        // Estadísticas para el gráfico
        $estadosEquipos = [
            'recibido' => Equipo::where('estado', 'recibido')->count(),
            'diagnostico' => Equipo::where('estado', 'diagnostico')->count(),
            'reparacion' => $enReparacion,
            'listo' => Equipo::where('estado', 'listo')->count(),
            'entregado' => $entregados,
        ];
        
        // Últimas entradas de equipos
        $ultimasEntradas = Equipo::with('cliente')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('dashboard.index', compact(
            'enReparacion',
            'entregados',
            'enGarantia',
            'pendientes',
            'estadosEquipos',
            'ultimasEntradas'
        ));
    }
}
