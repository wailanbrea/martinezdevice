<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    public function index()
    {
        $equipos = Equipo::with('cliente')->latest()->get();
        return response()->json($equipos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'descripcion_falla' => 'required|string',
            'estado' => 'nullable|in:recibido,diagnostico,reparacion,listo,entregado,garantia',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('equipos', 'public');
        }

        $equipo = Equipo::create($validated);

        // Generar QR después de crear
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $equipo->generarQR();
        }

        return response()->json($equipo->load('cliente'), 201);
    }

    public function show($id)
    {
        $equipo = Equipo::with(['cliente', 'reparaciones', 'historialEstados.usuario'])
            ->findOrFail($id);
        return response()->json($equipo);
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'descripcion_falla' => 'required|string',
            'estado' => 'nullable|in:recibido,diagnostico,reparacion,listo,entregado,garantia',
            'foto' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Eliminar foto anterior
            if ($equipo->foto) {
                Storage::disk('public')->delete($equipo->foto);
            }
            $validated['foto'] = $request->file('foto')->store('equipos', 'public');
        }

        $equipo->update($validated);

        return response()->json($equipo->load('cliente'));
    }

    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);

        if ($equipo->foto) {
            Storage::disk('public')->delete($equipo->foto);
        }

        $equipo->delete();

        return response()->json(null, 204);
    }

    public function generarQR($id)
    {
        $equipo = Equipo::findOrFail($id);
        $equipo->generarQR();

        return response()->json([
            'message' => 'QR generado exitosamente',
            'equipo' => $equipo,
        ]);
    }
}
