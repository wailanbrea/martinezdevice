<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Reparacion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['cliente', 'equipo', 'reparacion'])->latest()->get();
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $equipos = Equipo::with('cliente')->get();
        $reparaciones = Reparacion::with('equipo')->get();
        return view('facturas.create', compact('clientes', 'equipos', 'reparaciones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'equipo_id' => 'nullable|exists:equipos,id',
            'reparacion_id' => 'nullable|exists:reparaciones,id',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'forma_pago' => 'required|in:efectivo,transferencia,tarjeta',
            'fecha_emision' => 'required|date',
        ]);

        Factura::create($validated);

        return redirect()->route('facturas.index')->with('success', 'Factura creada exitosamente');
    }

    public function show($id)
    {
        $factura = Factura::with(['cliente', 'equipo', 'reparacion'])->findOrFail($id);
        return view('facturas.show', compact('factura'));
    }

    public function edit($id)
    {
        $factura = Factura::findOrFail($id);
        $clientes = Cliente::all();
        $equipos = Equipo::with('cliente')->get();
        $reparaciones = Reparacion::with('equipo')->get();
        return view('facturas.edit', compact('factura', 'clientes', 'equipos', 'reparaciones'));
    }

    public function update(Request $request, $id)
    {
        $factura = Factura::findOrFail($id);

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'equipo_id' => 'nullable|exists:equipos,id',
            'reparacion_id' => 'nullable|exists:reparaciones,id',
            'subtotal' => 'required|numeric|min:0',
            'impuestos' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'forma_pago' => 'required|in:efectivo,transferencia,tarjeta',
            'fecha_emision' => 'required|date',
        ]);

        $factura->update($validated);

        return redirect()->route('facturas.index')->with('success', 'Factura actualizada exitosamente');
    }

    public function destroy($id)
    {
        $factura = Factura::findOrFail($id);
        $factura->delete();

        return redirect()->route('facturas.index')->with('success', 'Factura eliminada exitosamente');
    }

    /**
     * Generar e imprimir PDF de la factura
     */
    public function imprimir($id)
    {
        $factura = Factura::with(['cliente', 'equipo', 'reparacion'])->findOrFail($id);

        $pdf = Pdf::loadView('facturas.pdf', compact('factura'));
        
        $numeroFactura = $factura->numero_factura ?? $factura->id;
        return $pdf->download("Factura_{$numeroFactura}.pdf");
    }
}
