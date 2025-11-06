<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reparacion;
use Illuminate\Http\Request;

class ReparacionController extends Controller
{
    public function index()
    {
        $reparaciones = Reparacion::with(['equipo.cliente', 'tecnico'])->latest()->get();
        return response()->json($reparaciones);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'tecnico_id' => 'required|exists:users,id',
            'diagnostico' => 'nullable|string',
            'piezas_reemplazadas' => 'nullable|array',
            'observaciones' => 'nullable|string',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'costo_piezas' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|in:pendiente,en_proceso,completada,cancelada',
        ]);

        $reparacion = Reparacion::create($validated);

        return response()->json($reparacion->load(['equipo.cliente', 'tecnico']), 201);
    }

    public function show($id)
    {
        $reparacion = Reparacion::with(['equipo.cliente', 'tecnico', 'factura', 'garantia'])
            ->findOrFail($id);
        return response()->json($reparacion);
    }

    public function update(Request $request, $id)
    {
        $reparacion = Reparacion::findOrFail($id);

        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'tecnico_id' => 'required|exists:users,id',
            'diagnostico' => 'nullable|string',
            'piezas_reemplazadas' => 'nullable|array',
            'observaciones' => 'nullable|string',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'costo_piezas' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date|after_or_equal:fecha_inicio',
            'estado' => 'nullable|in:pendiente,en_proceso,completada,cancelada',
        ]);

        $reparacion->update($validated);

        return response()->json($reparacion->load(['equipo.cliente', 'tecnico']));
    }

    public function destroy($id)
    {
        $reparacion = Reparacion::findOrFail($id);
        $reparacion->delete();

        return response()->json(null, 204);
    }
}
