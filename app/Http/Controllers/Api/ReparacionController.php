<?php

namespace App\Http\Controllers\Api;

use App\Models\Reparacion;
use App\Models\EstadoReparacion;
use App\Models\NotaReparacion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReparacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Optimizar: cargar solo campos necesarios
        $query = Reparacion::with([
            'equipo:id,cliente_id,marca,modelo,tipo',
            'equipo.cliente:id,nombre,telefono',
            'tecnico:id,firstname,lastname',
            'piezas:id,reparacion_id,nombre,cantidad,precio_unitario'
        ]);

        // Filtros
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

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo_reparacion', 'like', "%{$search}%")
                  ->orWhereHas('equipo.cliente', function($qc) use ($search) {
                      $qc->where('nombre', 'like', "%{$search}%");
                  })
                  ->orWhereHas('equipo', function($qe) use ($search) {
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

        // Crear registro en historial de estados
        EstadoReparacion::create([
            'reparacion_id' => $reparacion->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido y registrado en el sistema',
            'usuario_id' => auth()->id(),
        ]);

        // Optimizar: cargar solo campos necesarios
        $reparacion->load([
            'equipo:id,cliente_id,marca,modelo,tipo,numero_serie',
            'equipo.cliente:id,nombre,telefono,email',
            'tecnico:id,firstname,lastname'
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
        // Optimizar: cargar solo campos necesarios
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
            'pagos:id,reparacion_id,monto,fecha_pago'
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
            'estado' => 'nullable|in:Recibido,En Diagnóstico,Esperando Pieza,En Proceso,Finalizado,Entregado,Cancelado',
            'fecha_prometida' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'costo_diagnostico' => 'nullable|numeric|min:0',
            'costo_piezas' => 'nullable|numeric|min:0',
            'costo_mano_obra' => 'nullable|numeric|min:0',
        ]);

        // Si cambió el estado, registrar en historial
        if (isset($validated['estado']) && $validated['estado'] !== $reparacion->estado) {
            EstadoReparacion::create([
                'reparacion_id' => $reparacion->id,
                'estado' => $validated['estado'],
                'comentario' => $request->comentario ?? 'Estado actualizado',
                'usuario_id' => auth()->id(),
            ]);
        }

        // Calcular total estimado
        if (isset($validated['costo_diagnostico']) || isset($validated['costo_piezas']) || isset($validated['costo_mano_obra'])) {
            $validated['total_estimado'] = 
                ($validated['costo_diagnostico'] ?? $reparacion->costo_diagnostico) +
                ($validated['costo_piezas'] ?? $reparacion->costo_piezas) +
                ($validated['costo_mano_obra'] ?? $reparacion->costo_mano_obra);
        }

        $reparacion->update($validated);

        // Optimizar: cargar solo campos necesarios
        $reparacion->load([
            'equipo:id,cliente_id,marca,modelo,tipo',
            'equipo.cliente:id,nombre,telefono',
            'tecnico:id,firstname,lastname',
            'historialEstados:id,reparacion_id,estado,comentario,usuario_id,created_at',
            'historialEstados.usuario:id,firstname,lastname'
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
            'total_mes' => Reparacion::whereMonth('fecha_ingreso', Carbon::now()->month)->count(),
            'reparaciones_recientes' => Reparacion::with([
                'equipo:id,cliente_id,marca,modelo',
                'equipo.cliente:id,nombre',
                'tecnico:id,firstname,lastname'
            ])
                ->select('id', 'codigo_reparacion', 'equipo_id', 'tecnico_id', 'estado', 'fecha_ingreso', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json($stats, 200);
    }

    /**
     * Buscar reparación por código (para garantías)
     */
    public function buscarPorCodigo($codigo)
    {
        // Optimizar: cargar solo campos necesarios
        $reparacion = Reparacion::where('codigo_reparacion', $codigo)
            ->whereIn('estado', ['Finalizado', 'Entregado'])
            ->with([
                'equipo:id,cliente_id,tipo,marca,modelo,numero_serie,tipo_personalizado',
                'equipo.cliente:id,nombre',
                'tecnico:id,firstname,lastname',
                'reparacionesGarantia:id,reparacion_original_id,codigo_reparacion,estado,fecha_ingreso,fecha_vencimiento_garantia'
            ])
            ->first();

        if (!$reparacion) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró una reparación finalizada/entregada con ese código',
            ], 404);
        }

        // Verificar si esta reparación ya tiene garantías asociadas
        $tieneGarantias = $reparacion->reparacionesGarantia->count() > 0;
        $garantiasAsociadas = $reparacion->reparacionesGarantia->map(function($garantia) {
            $diasRestantes = null;
            $estadoGarantia = null;
            
            if ($garantia->fecha_vencimiento_garantia) {
                $diasRestantes = (int) Carbon::now()->diffInDays($garantia->fecha_vencimiento_garantia, false);
                if ($diasRestantes < 0) {
                    $estadoGarantia = 'vencida';
                    $diasRestantes = abs($diasRestantes);
                } elseif ($diasRestantes <= 7) {
                    $estadoGarantia = 'por_vencer';
                } else {
                    $estadoGarantia = 'vigente';
                }
            }
            
            return [
                'id' => $garantia->id,
                'codigo' => $garantia->codigo_reparacion,
                'estado' => $garantia->estado,
                'fecha_ingreso' => $garantia->fecha_ingreso?->format('d/m/Y'),
                'fecha_vencimiento' => $garantia->fecha_vencimiento_garantia?->format('d/m/Y'),
                'dias_restantes' => $diasRestantes,
                'estado_garantia' => $estadoGarantia,
            ];
        })->toArray();

        // Calcular días restantes de garantía de la reparación original
        $diasRestantesGarantia = null;
        $estadoGarantiaOriginal = null;
        if ($reparacion->fecha_vencimiento_garantia) {
            $diasRestantesGarantia = (int) Carbon::now()->diffInDays($reparacion->fecha_vencimiento_garantia, false);
            if ($diasRestantesGarantia < 0) {
                $estadoGarantiaOriginal = 'vencida';
                $diasRestantesGarantia = abs($diasRestantesGarantia);
            } elseif ($diasRestantesGarantia <= 7) {
                $estadoGarantiaOriginal = 'por_vencer';
            } else {
                $estadoGarantiaOriginal = 'vigente';
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $reparacion->id,
                'codigo_reparacion' => $reparacion->codigo_reparacion,
                'cliente_id' => $reparacion->equipo->cliente_id,
                'cliente_nombre' => $reparacion->equipo->cliente->nombre ?? 'N/A',
                'tipo' => $reparacion->equipo->tipo,
                'tipo_personalizado' => $reparacion->equipo->tipo_personalizado,
                'marca' => $reparacion->equipo->marca,
                'modelo' => $reparacion->equipo->modelo,
                'numero_serie' => $reparacion->equipo->numero_serie,
                'tecnico_id' => $reparacion->tecnico_id,
                'fecha_finalizacion' => $reparacion->fecha_finalizacion?->format('d/m/Y'),
                'fecha_vencimiento_garantia' => $reparacion->fecha_vencimiento_garantia?->format('d/m/Y'),
                'dias_restantes_garantia' => $diasRestantesGarantia,
                'estado_garantia_original' => $estadoGarantiaOriginal,
                'tiene_garantias' => $tieneGarantias,
                'cantidad_garantias' => count($garantiasAsociadas),
                'garantias_asociadas' => $garantiasAsociadas,
            ],
        ], 200);
    }
}
