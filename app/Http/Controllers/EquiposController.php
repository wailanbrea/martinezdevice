<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use App\Models\Cliente;

class EquiposController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Equipo::with(['cliente'])->withCount('reparaciones');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('marca', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%")
                  ->orWhere('codigo_unico', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($qc) use ($search) {
                      $qc->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            $query->where('estado', $request->estado);
        }

        // Filtro por tipo
        if ($request->has('tipo') && $request->tipo != '') {
            $query->where('tipo', $request->tipo);
        }

        $equipos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('equipos.index', compact('equipos'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $equipo = Equipo::with([
            'cliente',
            'reparaciones.tecnico',
            'reparaciones.recepcionista',
            'reparaciones.piezas',
            'reparaciones.notas.usuario',
            'reparaciones.historialEstados.usuario'
        ])->findOrFail($id);

        return view('equipos.show', compact('equipo'));
    }

    /**
     * Ver historial completo de un equipo
     */
    public function historial(string $id)
    {
        $equipo = Equipo::with([
            'cliente',
            'reparaciones' => function($query) {
                $query->orderBy('fecha_ingreso', 'desc');
            },
            'reparaciones.tecnico',
            'reparaciones.recepcionista',
            'reparaciones.piezas',
            'reparaciones.historialEstados.usuario',
            'reparaciones.factura',
        ])->findOrFail($id);

        return view('equipos.historial', compact('equipo'));
    }

    /**
     * Actualizar campo del equipo (solo admin)
     */
    public function actualizar(Request $request, string $id)
    {
        // Verificar que el usuario sea admin
        if (!auth()->user()->hasRole('administrador')) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }

        $equipo = Equipo::findOrFail($id);
        $campo = $request->input('campo');
        $valor = $request->input('valor');

        // Validar según el campo
        if ($campo === 'numero_serie') {
            // Validar que el número de serie no esté duplicado (si se proporciona)
            if (!empty($valor)) {
                $existe = Equipo::where('numero_serie', $valor)
                    ->where('id', '!=', $id)
                    ->exists();
                
                if ($existe) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Este número de serie ya está registrado para otro equipo.'
                    ], 422);
                }
            }
            $equipo->numero_serie = $valor;
        } elseif ($campo === 'marca_modelo') {
            // Separar marca y modelo (asumimos que el primer espacio separa marca de modelo)
            $partes = explode(' ', $valor, 2);
            $equipo->marca = $partes[0] ?? '';
            $equipo->modelo = $partes[1] ?? '';
        } else {
            return response()->json(['success' => false, 'message' => 'Campo no válido'], 400);
        }

        $equipo->save();

        return response()->json(['success' => true, 'message' => 'Campo actualizado exitosamente']);
    }
}
