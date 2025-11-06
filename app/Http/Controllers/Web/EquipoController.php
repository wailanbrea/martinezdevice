<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipo::with('cliente');

        // Búsqueda por texto (marca, modelo, número de serie, código único)
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->where(function($q) use ($busqueda) {
                $q->where('marca', 'like', "%{$busqueda}%")
                  ->orWhere('modelo', 'like', "%{$busqueda}%")
                  ->orWhere('numero_serie', 'like', "%{$busqueda}%")
                  ->orWhere('codigo_unico', 'like', "%{$busqueda}%")
                  ->orWhere('tipo', 'like', "%{$busqueda}%");
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por cliente
        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $equipos = $query->latest()->get();
        $clientes = Cliente::orderBy('nombre')->get();
        $tipos = Equipo::distinct()->pluck('tipo')->filter()->sort()->values();

        return view('equipos.index', compact('equipos', 'clientes', 'tipos'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        return view('equipos.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        // Validar sin cliente_id primero para permitir 'generico'
        $validated = $request->validate([
            'tipo' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'descripcion_falla' => 'required|string',
            'estado' => 'nullable|in:recibido,diagnostico,reparacion,listo,entregado,garantia',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Resolver cliente (id o 'generico')
        $clienteIdEntrada = $request->input('cliente_id');
        if ($clienteIdEntrada === 'generico') {
            $clienteGenerico = Cliente::firstOrCreate(
                ['cedula_rnc' => 'GEN-00000000'],
                ['nombre' => 'Cliente Genérico', 'telefono' => null, 'correo' => null, 'direccion' => null]
            );
            $validated['cliente_id'] = $clienteGenerico->id;
        } else {
            // Validar que exista el cliente
            $request->validate(['cliente_id' => 'required|exists:clientes,id']);
            $validated['cliente_id'] = (int) $clienteIdEntrada;
        }

        // Manejar subida de foto
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nombreArchivo = 'equipo_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $ruta = $foto->storeAs('equipos', $nombreArchivo, 'public');
            $validated['foto'] = $ruta;
        }

        $equipo = Equipo::create($validated);

        // Generar QR automáticamente
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $equipo->generarQR();
        }

        return redirect()->route('equipos.index')->with('success', 'Entrada creada exitosamente');
    }

    public function show($id)
    {
        $equipo = Equipo::with(['cliente', 'reparaciones', 'historialEstados'])->findOrFail($id);
        return view('equipos.show', compact('equipo'));
    }

    public function edit($id)
    {
        $equipo = Equipo::findOrFail($id);
        $clientes = Cliente::all();
        return view('equipos.edit', compact('equipo', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);

        // Validar sin cliente_id primero para permitir 'generico'
        $validated = $request->validate([
            'tipo' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'modelo' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'descripcion_falla' => 'required|string',
            'estado' => 'nullable|in:recibido,diagnostico,reparacion,listo,entregado,garantia',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Resolver cliente (id o 'generico')
        $clienteIdEntrada = $request->input('cliente_id');
        if ($clienteIdEntrada === 'generico') {
            $clienteGenerico = Cliente::firstOrCreate(
                ['cedula_rnc' => 'GEN-00000000'],
                ['nombre' => 'Cliente Genérico', 'telefono' => null, 'correo' => null, 'direccion' => null]
            );
            $validated['cliente_id'] = $clienteGenerico->id;
        } else {
            // Validar que exista el cliente
            $request->validate(['cliente_id' => 'required|exists:clientes,id']);
            $validated['cliente_id'] = (int) $clienteIdEntrada;
        }

        // Manejar subida de foto
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($equipo->foto && Storage::disk('public')->exists($equipo->foto)) {
                Storage::disk('public')->delete($equipo->foto);
            }

            $foto = $request->file('foto');
            $nombreArchivo = 'equipo_' . time() . '_' . uniqid() . '.' . $foto->getClientOriginalExtension();
            $ruta = $foto->storeAs('equipos', $nombreArchivo, 'public');
            $validated['foto'] = $ruta;
        }

        $equipo->update($validated);

        return redirect()->route('equipos.index')->with('success', 'Entrada actualizada exitosamente');
    }

    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);
        
        // Eliminar foto si existe
        if ($equipo->foto && Storage::disk('public')->exists($equipo->foto)) {
            Storage::disk('public')->delete($equipo->foto);
        }
        
        $equipo->delete();

        return redirect()->route('equipos.index')->with('success', 'Entrada eliminada exitosamente');
    }

    /**
     * Generar e imprimir código QR del equipo
     */
    public function imprimirQR($id)
    {
        $equipo = Equipo::findOrFail($id);
        
        // Generar QR si no existe
        if (empty($equipo->codigo_qr) && class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $equipo->generarQR();
        }

        if (!class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            return redirect()->route('equipos.show', $equipo->id)
                ->with('error', 'La librería de QR no está disponible');
        }

        $url = url("/api/public/status/{$equipo->codigo_unico}");
        
        return response()->streamDownload(function () use ($url) {
            echo \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(300)
                ->margin(2)
                ->generate($url);
        }, "QR_{$equipo->codigo_unico}.png", [
            'Content-Type' => 'image/png',
        ]);
    }
}
