<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Factura;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['cliente', 'equipo', 'reparacion'])->latest()->get();
        return response()->json($facturas);
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

        $factura = Factura::create($validated);

        return response()->json($factura->load(['cliente', 'equipo', 'reparacion']), 201);
    }

    public function show($id)
    {
        $factura = Factura::with(['cliente', 'equipo', 'reparacion', 'pagos'])
            ->findOrFail($id);
        return response()->json($factura);
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

        return response()->json($factura->load(['cliente', 'equipo', 'reparacion']));
    }

    public function destroy($id)
    {
        $factura = Factura::findOrFail($id);
        $factura->delete();

        return response()->json(null, 204);
    }

    public function descargar($id)
    {
        $factura = Factura::with(['cliente', 'equipo', 'reparacion'])->findOrFail($id);

        $pdf = Pdf::loadView('facturas.pdf', compact('factura'));
        return $pdf->download("factura-{$factura->numero_factura}.pdf");
    }

    public function imprimir($id)
    {
        $factura = Factura::with(['cliente', 'equipo', 'reparacion'])->findOrFail($id);

        $pdf = Pdf::loadView('facturas.ticket', compact('factura'));
        return $pdf->stream("factura-{$factura->numero_factura}.pdf");
    }
}
