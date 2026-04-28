<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reparacion;
use App\Models\EstadoReparacion;
use App\Models\NotaReparacion;
use App\Models\Equipo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ActividadUsuarioController extends Controller
{
    /**
     * Mostrar lista de usuarios con actividad
     */
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        // Obtener usuarios que tienen actividad (reparaciones, estados, notas)
        // Mostrar todos los usuarios con roles relevantes
        $query = User::whereHas('roles', function($q) {
            $q->whereIn('slug', ['tecnico', 'recepcionista', 'administrador']);
        })->with('roles');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $usuarios = $query->orderBy('firstname', 'asc')->paginate(15);

        // Agregar estadísticas a cada usuario
        $usuarios->getCollection()->transform(function($usuario) {
            $usuario->stats = $this->getUserStats($usuario->id);
            return $usuario;
        });

        return view('actividad-usuarios.index', compact('usuarios', 'search'));
    }

    /**
     * Mostrar actividad detallada de un usuario
     */
    public function show(Request $request, $id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        $fechaInicio = $request->fecha_inicio ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $fechaFin = $request->fecha_fin ?? Carbon::now()->endOfMonth()->format('Y-m-d');

        // Estadísticas generales
        $stats = $this->getUserStats($usuario->id, $fechaInicio, $fechaFin);

        // Reparaciones creadas (como recepcionista)
        $reparacionesCreadas = Reparacion::with(['equipo.cliente', 'tecnico'])
            ->where('recepcionista_id', $usuario->id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'creadas');

        // Reparaciones completadas (como técnico)
        $reparacionesCompletadas = Reparacion::with(['equipo.cliente', 'factura'])
            ->where('tecnico_completo_id', $usuario->id)
            ->whereIn('estado', ['Finalizado', 'Entregado'])
            ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_finalizacion', 'desc')
            ->paginate(10, ['*'], 'completadas');

        // Reparaciones asignadas (como técnico)
        $reparacionesAsignadas = Reparacion::with(['equipo.cliente'])
            ->where('tecnico_id', $usuario->id)
            ->whereNotIn('estado', ['Finalizado', 'Entregado', 'Cancelado'])
            ->orderBy('fecha_ingreso', 'asc')
            ->paginate(10, ['*'], 'asignadas');

        // Cambios de estado realizados
        $cambiosEstado = EstadoReparacion::with(['reparacion.equipo.cliente'])
            ->where('usuario_id', $usuario->id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'estados');

        // Notas agregadas
        $notasAgregadas = NotaReparacion::with(['reparacion.equipo.cliente'])
            ->where('usuario_id', $usuario->id)
            ->whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'notas');

        // Comisiones generadas (detalladas)
        $comisionesDetalladas = Reparacion::with(['equipo.cliente', 'factura'])
            ->where('tecnico_completo_id', $usuario->id)
            ->whereNotNull('monto_comision')
            ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
            ->orderBy('fecha_finalizacion', 'desc')
            ->paginate(15, ['*'], 'comisiones');

        return view('actividad-usuarios.show', compact(
            'usuario',
            'stats',
            'reparacionesCreadas',
            'reparacionesCompletadas',
            'reparacionesAsignadas',
            'cambiosEstado',
            'notasAgregadas',
            'comisionesDetalladas',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Obtener estadísticas de un usuario
     */
    private function getUserStats($userId, $fechaInicio = null, $fechaFin = null)
    {
        $fechaInicio = $fechaInicio ?? Carbon::now()->startOfYear()->format('Y-m-d');
        $fechaFin = $fechaFin ?? Carbon::now()->endOfYear()->format('Y-m-d');

        return [
            // Reparaciones creadas (como recepcionista)
            'reparaciones_creadas' => Reparacion::where('recepcionista_id', $userId)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count(),

            // Reparaciones completadas (como técnico)
            'reparaciones_completadas' => Reparacion::where('tecnico_completo_id', $userId)
                ->whereIn('estado', ['Finalizado', 'Entregado'])
                ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                ->count(),

            // Reparaciones asignadas actualmente
            'reparaciones_asignadas' => Reparacion::where('tecnico_id', $userId)
                ->whereNotIn('estado', ['Finalizado', 'Entregado', 'Cancelado'])
                ->count(),

            // Total de comisiones generadas
            'total_comisiones' => Reparacion::where('tecnico_completo_id', $userId)
                ->whereNotNull('monto_comision')
                ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                ->sum('monto_comision'),

            // Ingresos generados (reparaciones completadas)
            'ingresos_generados' => Reparacion::where('tecnico_completo_id', $userId)
                ->whereIn('estado', ['Finalizado', 'Entregado'])
                ->whereBetween('fecha_finalizacion', [$fechaInicio, $fechaFin])
                ->sum(DB::raw('COALESCE(precio_cotizado, total_estimado)')),

            // Cambios de estado realizados
            'cambios_estado' => EstadoReparacion::where('usuario_id', $userId)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count(),

            // Notas agregadas
            'notas_agregadas' => NotaReparacion::where('usuario_id', $userId)
                ->whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->count(),
        ];
    }
}
