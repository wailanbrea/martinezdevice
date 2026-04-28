<?php

namespace App\Http\Controllers\Api;

use App\Models\Cliente;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Optimizar: cargar solo campos necesarios
        $query = Cliente::with([
            'equipos:id,cliente_id,marca,modelo,tipo'
        ]);

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%")
                  ->orWhere('cedula_rnc', 'like', "%{$search}%");
            });
        }

        $clientes = $query->paginate(15);

        return response()->json($clientes, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula_rnc' => 'nullable|string|max:50|unique:clientes,cedula_rnc',
            'telefono' => 'required|string|max:50|unique:clientes,telefono',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string',
        ], [
            'telefono.unique' => 'Este teléfono ya está registrado para otro cliente.',
            'email.unique' => 'Este correo electrónico ya está registrado para otro cliente.',
            'cedula_rnc.unique' => 'Esta cédula/RNC ya está registrada para otro cliente.',
        ]);

        // Usar transacción con lock para prevenir race conditions
        $cliente = DB::transaction(function () use ($validated) {
            // Verificar nuevamente dentro de la transacción (double-check) con lock
            if (isset($validated['telefono']) && Cliente::where('telefono', $validated['telefono'])->lockForUpdate()->first()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'telefono' => ['Este teléfono ya está registrado para otro cliente.']
                ]);
            }
            
            if (isset($validated['email']) && !empty($validated['email']) && Cliente::where('email', $validated['email'])->lockForUpdate()->first()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'email' => ['Este correo electrónico ya está registrado para otro cliente.']
                ]);
            }
            
            if (isset($validated['cedula_rnc']) && !empty($validated['cedula_rnc']) && Cliente::where('cedula_rnc', $validated['cedula_rnc'])->lockForUpdate()->first()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'cedula_rnc' => ['Esta cédula/RNC ya está registrada para otro cliente.']
                ]);
            }
            
            return Cliente::create($validated);
        });

        return response()->json([
            'message' => 'Cliente creado exitosamente',
            'data' => $cliente,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        // Optimizar: cargar solo campos necesarios
        $cliente->load([
            'equipos:id,cliente_id,marca,modelo,tipo,numero_serie',
            'equipos.reparaciones:id,equipo_id,codigo_reparacion,estado,fecha_ingreso'
        ]);

        return response()->json([
            'data' => $cliente,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|required|string|max:255',
            'cedula_rnc' => 'nullable|string|max:50|unique:clientes,cedula_rnc,' . $cliente->id,
            'telefono' => 'sometimes|required|string|max:50|unique:clientes,telefono,' . $cliente->id,
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $cliente->id,
            'direccion' => 'nullable|string',
        ], [
            'telefono.unique' => 'Este teléfono ya está registrado para otro cliente.',
            'email.unique' => 'Este correo electrónico ya está registrado para otro cliente.',
            'cedula_rnc.unique' => 'Esta cédula/RNC ya está registrada para otro cliente.',
        ]);

        $cliente->update($validated);

        return response()->json([
            'message' => 'Cliente actualizado exitosamente',
            'data' => $cliente,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return response()->json([
            'message' => 'Cliente eliminado exitosamente',
        ], 200);
    }
}
