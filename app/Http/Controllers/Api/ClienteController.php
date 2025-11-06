<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('equipos')->latest()->get();
        return response()->json($clientes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula_rnc' => 'required|string|unique:clientes,cedula_rnc',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
        ]);

        $cliente = Cliente::create($validated);

        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = Cliente::with('equipos')->findOrFail($id);
        return response()->json($cliente);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula_rnc' => 'required|string|unique:clientes,cedula_rnc,' . $id,
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
        ]);

        $cliente->update($validated);

        return response()->json($cliente);
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        return response()->json(null, 204);
    }
}
