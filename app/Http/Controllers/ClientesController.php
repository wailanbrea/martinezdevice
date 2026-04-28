<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::withCount('equipos');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%")
                  ->orWhere('cedula_rnc', 'like', "%{$search}%");
            });
        }

        $clientes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.create');
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

        // Limpiar caché de clientes
        Cache::forget('clientes.list');

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::with(['equipos.reparaciones'])->findOrFail($id);

        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Cliente::findOrFail($id);

        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula_rnc' => 'nullable|string|max:50|unique:clientes,cedula_rnc,' . $id,
            'telefono' => 'required|string|max:50|unique:clientes,telefono,' . $id,
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $id,
            'direccion' => 'nullable|string',
        ], [
            'telefono.unique' => 'Este teléfono ya está registrado para otro cliente.',
            'email.unique' => 'Este correo electrónico ya está registrado para otro cliente.',
            'cedula_rnc.unique' => 'Esta cédula/RNC ya está registrada para otro cliente.',
        ]);

        $cliente->update($validated);

        // Limpiar caché de clientes
        Cache::forget('clientes.list');

        return redirect()->route('clientes.show', $cliente)
            ->with('success', 'Cliente actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->delete();

        // Limpiar caché de clientes
        Cache::forget('clientes.list');

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }
}
