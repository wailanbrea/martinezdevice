<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EstadoReparacion;
use App\Models\NotaReparacion;
use App\Models\Reparacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReparacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reparacion::with([
            'equipo:id,cliente_id,marca,modelo,tipo',
            'equipo.cliente:id,nombre,telefono',
            'tecnico:id,firstname,lastname',
            'piezas:id,reparacion_id,nombre,cantidad,precio_unitario',
        ]);

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('tecnico_id')) {
            $query->where('tecnico_id', $request->tecnico_id);
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_hasta);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_reparacion', 'like', "%{$search}%")
                    ->orWhereHas('equipo.cliente', function ($qc) use ($search) {
                        $qc->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('equipo', function ($qe) use ($search) {
                        $qe->where('marca', 'like', "%{$search}%")
                            ->orWhere('modelo', 'like', "%{$search}%");
                    });
            });
        }

        $reparaciones = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($reparaciones, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'tecnico_id' => 'nullable|exists:users,id',
            'fecha_ingreso' => 'required|date',
            'fecha_prometida' => 'nullable|date',
            'costo_diagnostico' => 'nullable|numeric|min:0',
        ]);

        $validated['estado'] = 'Recibido';
        $validated['fecha_ingreso'] = $validated['fecha_ingreso'] ?? Carbon::now()->format('Y-m-d');

        $reparacion = Reparacion::create($validated);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido y registrado en el sistema',
            'usuario_id' => auth()->id(),
        ]);

        $reparacion->load([
            'equipo:id,cliente_id,marca,modelo,tipo,numero_serie',
            'equipo.cliente:id,nombre,telefono,email',
            'tecnico:id,firstname,lastname',
        ]);

        return response()->json([
            'message' => 'Reparación creada exitosamente',
            'data' => $reparacion,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reparacion $reparacion)
    {
        $reparacion->load([
            'equipo:id,cliente_id,tipo,marca,modelo,numero_serie,descripcion_problema',
            'equipo.cliente:id,nombre,telefono,email',
            'tecnico:id,firstname,lastname',
            'piezas:id,reparacion_id,nombre,cantidad,precio_unitario',
            'notas:id,reparacion_id,usuario_id,nota,created_at',
            'notas.usuario:id,firstname,lastname',
            'historialEstados:id,reparacion_id,estado,comentario,usuario_id,created_at',
            'historialEstados.usuario:id,firstname,lastname',
            'factura:id,reparacion_id,numero_factura,total',
            'pagos:id,reparacion_id,monto,fecha_pago',
        ]);

        return response()->json([
            'data' => $reparacion,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reparacion $reparacion)
    {
        $validated = $request->validate([
            'tecnico_id' => 'nullable|exists:users,id',
            'estado' => 'nullable|in:Recibido,En Diagnóstico,Pendiente Revisión Admin,Esperando Aprobación,Aprobado,Esperando Pieza,En Proceso,Finalizado,Sin Reparación,Entregado,Cancelado',
            'fecha_prometida' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'costo_diagnostico' => 'nullable|numeric|min:0',
            'costo_piezas' => 'nullable|numeric|min:0',
            'costo_mano_obra' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['estado']) && $validated['estado'] !== $reparacion->estado) {
            EstadoReparacion::create([
                'reparacion_id' => $reparacion->id,
                'estado' => $validated['estado'],
                'comentario' => $request->comentario ?? 'Estado actualizado',
                'usuario_id' => auth()->id(),
            ]);
        }

        if (isset($validated['costo_diagnostico']) || isset($validated['costo_piezas']) || isset($validated['costo_mano_obra'])) {
            $validated['total_estimado'] =
                ($validated['costo_diagnostico'] ?? $reparacion->costo_diagnostico) +
                ($validated['costo_piezas'] ?? $reparacion->costo_piezas) +
                ($validated['costo_mano_obra'] ?? $reparacion->costo_mano_obra);
        }

        $reparacion->update($validated);

        $reparacion->load([
            'equipo:id,cliente_id,marca,modelo,tipo',
            'equipo.cliente:id,nombre,telefono',
            'tecnico:id,firstname,lastname',
            'historialEstados:id,reparacion_id,estado,comentario,usuario_id,created_at',
            'historialEstados.usuario:id,firstname,lastname',
        ]);

        return response()->json([
            'message' => 'Reparación actualizada exitosamente',
            'data' => $reparacion,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reparacion $reparacion)
    {
        $reparacion->delete();

        return response()->json([
            'message' => 'Reparación eliminada exitosamente',
        ], 200);
    }

    /**
     * Agregar nota a una reparación
     */
    public function addNota(Request $request, Reparacion $reparacion)
    {
        $validated = $request->validate([
            'nota' => 'required|string',
        ]);

        $nota = NotaReparacion::create([
            'reparacion_id' => $reparacion->id,
            'usuario_id' => auth()->id(),
            'nota' => $validated['nota'],
        ]);

        $nota->load('usuario');

        return response()->json([
            'message' => 'Nota agregada exitosamente',
            'data' => $nota,
        ], 201);
    }

    /**
     * Obtener estadísticas del dashboard
     */
    public function estadisticas()
    {
        $stats = [
            'en_progreso' => Reparacion::where('estado', 'En Proceso')->count(),
            'pendientes_revision' => Reparacion::where('estado', 'Recibido')->count(),
            'completadas' => Reparacion::whereIn('estado', ['Finalizado', 'Entregado'])->count(),
            'total_mes' => Reparacion::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats, 200);
    }

    /**
     * Buscar reparación por código
     */
    public function buscarPorCodigo($codigo)
    {
        $reparacion = Reparacion::where('codigo_reparacion', $codigo)
            ->with([
                'equipo:id,cliente_id,tipo,marca,modelo,numero_serie,descripcion_problema,codigo_unico',
                'equipo.cliente:id,nombre,telefono,email',
                'tecnico:id,firstname,lastname',
                'recepcionista:id,firstname,lastname',
                'historialEstados:id,reparacion_id,estado,comentario,usuario_id,created_at',
                'historialEstados.usuario:id,firstname,lastname',
                'factura:id,reparacion_id,numero_factura,total',
            ])
            ->first();

        if (!$reparacion) {
            return response()->json([
                'message' => 'Reparación no encontrada',
            ], 404);
        }

        return response()->json([
            'data' => $reparacion,
        ], 200);
    }
}
