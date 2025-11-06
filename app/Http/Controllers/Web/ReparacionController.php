<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Reparacion;
use App\Models\Equipo;
use App\Models\User;
use Illuminate\Http\Request;

class ReparacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Reparacion::with(['equipo.cliente', 'tecnico']);

        // Búsqueda por texto (diagnóstico, observaciones)
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('diagnostico', 'like', "%{$busqueda}%")
                  ->orWhere('observaciones', 'like', "%{$busqueda}%")
                  ->orWhereHas('equipo', function($eq) use ($busqueda) {
                      $eq->where('marca', 'like', "%{$busqueda}%")
                         ->orWhere('modelo', 'like', "%{$busqueda}%")
                         ->orWhere('codigo_unico', 'like', "%{$busqueda}%");
                  })
                  ->orWhereHas('equipo.cliente', function($cl) use ($busqueda) {
                      $cl->where('nombre', 'like', "%{$busqueda}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por técnico
        if ($request->filled('tecnico_id')) {
            $query->where('tecnico_id', $request->tecnico_id);
        }

        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->whereHas('equipo', function($q) use ($request) {
                $q->where('cliente_id', $request->cliente_id);
            });
        }

        // Filtro por rango de fechas (fecha de inicio)
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_inicio', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_inicio', '<=', $request->fecha_hasta);
        }

        // Filtro por rango de costos
        if ($request->filled('costo_min')) {
            $query->where('total', '>=', $request->costo_min);
        }
        if ($request->filled('costo_max')) {
            $query->where('total', '<=', $request->costo_max);
        }

        $reparaciones = $query->latest()->get();
        
        // Obtener datos para los filtros
        $tecnicos = User::whereHas('roles', function($q) {
            $q->where('slug', 'tecnico');
        })->orderBy('name')->get();
        
        $clientes = \App\Models\Cliente::orderBy('nombre')->get();

        return view('reparaciones.index', compact('reparaciones', 'tecnicos', 'clientes'));
    }

    public function create()
    {
        $equipos = Equipo::with('cliente')->get();
        $tecnicos = User::whereHas('roles', function($q) {
            $q->where('slug', 'tecnico');
        })->get();
        return view('reparaciones.create', compact('equipos', 'tecnicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'tecnico_id' => 'required|exists:users,id',
            'diagnostico' => 'nullable|string',
            'piezas_reemplazadas' => 'nullable|string', // JSON string desde el formulario
            'observaciones' => 'nullable|string',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'costo_piezas' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'estado' => 'nullable|in:pendiente,en_proceso,completada,cancelada',
        ]);

        // Procesar piezas_reemplazadas si viene como JSON string
        if ($request->filled('piezas_reemplazadas')) {
            $piezasJson = json_decode($request->piezas_reemplazadas, true);
            if (is_array($piezasJson)) {
                // Validar que cada pieza tenga nombre y precio
                $piezasValidadas = [];
                $costoTotalPiezas = 0;
                foreach ($piezasJson as $pieza) {
                    if (isset($pieza['nombre']) && !empty(trim($pieza['nombre']))) {
                        $precio = floatval($pieza['precio'] ?? 0);
                        $piezasValidadas[] = [
                            'nombre' => trim($pieza['nombre']),
                            'precio' => $precio
                        ];
                        $costoTotalPiezas += $precio;
                    }
                }
                $validated['piezas_reemplazadas'] = $piezasValidadas;
                // Calcular costo total de piezas si no se envió
                if (!$request->filled('costo_piezas')) {
                    $validated['costo_piezas'] = $costoTotalPiezas;
                }
            } else {
                $validated['piezas_reemplazadas'] = null;
            }
        } else {
            $validated['piezas_reemplazadas'] = null;
        }

        Reparacion::create($validated);

        return redirect()->route('reparaciones.index')->with('success', 'Reparación creada exitosamente');
    }

    public function show($id)
    {
        $reparacion = Reparacion::with(['equipo.cliente', 'tecnico'])->findOrFail($id);
        return view('reparaciones.show', compact('reparacion'));
    }

    public function edit($id)
    {
        $reparacion = Reparacion::findOrFail($id);
        $equipos = Equipo::with('cliente')->get();
        $tecnicos = User::whereHas('roles', function($q) {
            $q->where('slug', 'tecnico');
        })->get();
        return view('reparaciones.edit', compact('reparacion', 'equipos', 'tecnicos'));
    }

    public function update(Request $request, $id)
    {
        $reparacion = Reparacion::findOrFail($id);

        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'tecnico_id' => 'required|exists:users,id',
            'diagnostico' => 'nullable|string',
            'piezas_reemplazadas' => 'nullable|string', // JSON string desde el formulario
            'observaciones' => 'nullable|string',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'costo_piezas' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_finalizacion' => 'nullable|date',
            'estado' => 'nullable|in:pendiente,en_proceso,completada,cancelada',
        ]);

        // Procesar piezas_reemplazadas si viene como JSON string
        if ($request->filled('piezas_reemplazadas')) {
            $piezasJson = json_decode($request->piezas_reemplazadas, true);
            if (is_array($piezasJson)) {
                // Validar que cada pieza tenga nombre y precio
                $piezasValidadas = [];
                $costoTotalPiezas = 0;
                foreach ($piezasJson as $pieza) {
                    if (isset($pieza['nombre']) && !empty(trim($pieza['nombre']))) {
                        $precio = floatval($pieza['precio'] ?? 0);
                        $piezasValidadas[] = [
                            'nombre' => trim($pieza['nombre']),
                            'precio' => $precio
                        ];
                        $costoTotalPiezas += $precio;
                    }
                }
                $validated['piezas_reemplazadas'] = $piezasValidadas;
                // Calcular costo total de piezas si no se envió
                if (!$request->filled('costo_piezas')) {
                    $validated['costo_piezas'] = $costoTotalPiezas;
                }
            } else {
                $validated['piezas_reemplazadas'] = null;
            }
        } else {
            $validated['piezas_reemplazadas'] = null;
        }

        $reparacion->update($validated);

        return redirect()->route('reparaciones.index')->with('success', 'Reparación actualizada exitosamente');
    }

    public function destroy($id)
    {
        $reparacion = Reparacion::findOrFail($id);
        $reparacion->delete();

        return redirect()->route('reparaciones.index')->with('success', 'Reparación eliminada exitosamente');
    }
}
