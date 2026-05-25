<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaConfiguracion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    /**
     * Listar todas las facturas
     */
    public function index(Request $request)
    {
        $query = Factura::with(['cliente', 'equipo', 'reparacion']);

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_factura', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($qc) use ($search) {
                      $qc->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por fecha
        if ($request->has('fecha_inicio') && $request->fecha_inicio != '') {
            $query->where('fecha_emision', '>=', $request->fecha_inicio);
        }

        if ($request->has('fecha_fin') && $request->fecha_fin != '') {
            $query->where('fecha_emision', '<=', $request->fecha_fin);
        }

        // Ordenar por fecha de emisión descendente (más recientes primero)
        $facturas = $query->orderBy('fecha_emision', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate(15);

        return view('facturas.index', compact('facturas'));
    }

    /**
     * Mostrar la factura
     */
    public function show(Factura $factura)
    {
        $factura->load(['cliente', 'equipo', 'reparacion']);
        $configuracion = FacturaConfiguracion::obtener();
        $mostrarImpuestos = $configuracion->impuestosHabilitados();
        
        return view('facturas.show', compact('factura', 'configuracion', 'mostrarImpuestos'));
    }

    /**
     * Generar PDF de la factura (formato nuevo)
     */
    public function pdf(Factura $factura)
    {
        $factura->load(['cliente', 'equipo', 'reparacion.recepcionista', 'reparacion.tecnico', 'reparacion.tecnicoCompleto', 'reparacion.historialEstados.usuario']);
        $configuracion = FacturaConfiguracion::obtener();
        $recibidoPor = $this->obtenerRecibidoPor($factura);
        $preparadoPor = $this->obtenerPreparadoPor($factura);
        $mostrarImpuestos = $configuracion->impuestosHabilitados();
        $pdf = Pdf::loadView('facturas.formato', compact('factura', 'configuracion', 'recibidoPor', 'preparadoPor', 'mostrarImpuestos'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream('factura-' . $factura->numero_factura . '.pdf');
    }

    /**
     * Generar PDF y obtener URL para compartir por WhatsApp
     */
    public function compartirWhatsApp(Factura $factura)
    {
        $factura->load(['cliente', 'equipo', 'reparacion.recepcionista', 'reparacion.tecnico', 'reparacion.tecnicoCompleto', 'reparacion.historialEstados.usuario']);
        $configuracion = FacturaConfiguracion::obtener();
        $recibidoPor = $this->obtenerRecibidoPor($factura);
        $preparadoPor = $this->obtenerPreparadoPor($factura);
        $mostrarImpuestos = $configuracion->impuestosHabilitados();
        $pdf = Pdf::loadView('facturas.formato', compact('factura', 'configuracion', 'recibidoPor', 'preparadoPor', 'mostrarImpuestos'));
        $pdf->setPaper('A4', 'portrait');
        
        // Guardar PDF temporalmente
        $nombreArchivo = 'factura-' . $factura->numero_factura . '-' . time() . '.pdf';
        $rutaArchivo = storage_path('app/public/temp/' . $nombreArchivo);
        
        // Crear directorio si no existe
        if (!file_exists(dirname($rutaArchivo))) {
            mkdir(dirname($rutaArchivo), 0755, true);
        }
        
        $pdf->save($rutaArchivo);
        
        // URL pública del PDF (usar URL completa)
        $urlPdf = url('storage/temp/' . $nombreArchivo);
        
        // Número de teléfono del cliente (limpiar formato)
        $telefono = preg_replace('/[^0-9]/', '', $factura->cliente->telefono ?? '');
        
        if (empty($telefono)) {
            return back()->with('error', 'El cliente no tiene un número de teléfono registrado.');
        }
        
        // Nombre de empresa: usar Martinez Devices (no MartinezService) en mensajes a WhatsApp
        $empresaNombre = $configuracion->empresa_nombre ?? 'Martinez Devices';
        if (stripos($empresaNombre, 'MartinezService') !== false) {
            $empresaNombre = 'Martinez Devices';
        }
        // Mensaje para WhatsApp
        $mensaje = "Hola, te comparto la factura #{$factura->numero_factura} de {$empresaNombre}.\n\n";
        $mensaje .= "Total: {$configuracion->simbolo_moneda}" . number_format($factura->total, 2) . "\n\n";
        $mensaje .= "Puedes descargar la factura en PDF desde: {$urlPdf}";
        
        // URL de WhatsApp Web/App
        $urlWhatsApp = "https://wa.me/{$telefono}?text=" . urlencode($mensaje);
        
        return redirect($urlWhatsApp);
    }

    /**
     * Mostrar formulario de edición (solo admin)
     */
    public function edit(Factura $factura)
    {
        // Verificar que el usuario sea administrador
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'No tienes permisos para editar facturas.');
        }

        $factura->load(['cliente', 'equipo', 'reparacion']);
        $configuracion = FacturaConfiguracion::obtener();
        $mostrarImpuestos = $configuracion->impuestosHabilitados();
        
        return view('facturas.edit', compact('factura', 'configuracion', 'mostrarImpuestos'));
    }

    /**
     * Actualizar factura (solo admin)
     */
    public function update(Request $request, Factura $factura)
    {
        // Verificar que el usuario sea administrador
        if (!auth()->user()->hasRole('administrador')) {
            abort(403, 'No tienes permisos para editar facturas.');
        }

        $validated = $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'aplicar_impuesto' => 'nullable|boolean',
            'ncf' => 'nullable|string|max:50',
            'impuestos' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'forma_pago' => 'required|string|in:efectivo,transferencia,tarjeta,cheque',
            'fecha_emision' => 'required|date',
        ], [
            'subtotal.required' => 'El subtotal es obligatorio.',
            'subtotal.numeric' => 'El subtotal debe ser un número.',
            'subtotal.min' => 'El subtotal no puede ser negativo.',
            'aplicar_impuesto.boolean' => 'El campo aplicar impuesto debe ser verdadero o falso.',
            'ncf.max' => 'El NCF no puede exceder 50 caracteres.',
            'impuestos.required' => 'Los impuestos son obligatorios.',
            'impuestos.numeric' => 'Los impuestos deben ser un número.',
            'impuestos.min' => 'Los impuestos no pueden ser negativos.',
            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total no puede ser negativo.',
            'forma_pago.required' => 'La forma de pago es obligatoria.',
            'forma_pago.in' => 'La forma de pago seleccionada no es válida.',
            'fecha_emision.required' => 'La fecha de emisión es obligatoria.',
            'fecha_emision.date' => 'La fecha de emisión debe ser una fecha válida.',
        ]);

        $configuracion = FacturaConfiguracion::obtener();
        $aplicarImpuesto = $configuracion->debeAplicarImpuesto($request->has('aplicar_impuesto') && $request->aplicar_impuesto == '1');
        $subtotal = (float) $validated['subtotal'];

        if ($aplicarImpuesto) {
            $porcentajeImpuesto = $configuracion->porcentajeImpuestoActivo();
            $validated['impuestos'] = round(($subtotal * $porcentajeImpuesto) / 100, 2);
            $validated['total'] = $subtotal + $validated['impuestos'];
            $ncfCodigo = \App\Models\SistemaConfiguracion::obtenerNcfCodigo();
            if (empty(trim((string) ($validated['ncf'] ?? ''))) && $ncfCodigo) {
                $ultimoNCF = Factura::whereNotNull('ncf')->where('id', '!=', $factura->id)->lockForUpdate()->max('id') ?? 0;
                $validated['ncf'] = $ncfCodigo . str_pad($ultimoNCF + 1, 8, '0', STR_PAD_LEFT);
            }
        } else {
            // Sin impuesto: forzar 0 y total = subtotal para que no se refleje impuesto en PDF/WhatsApp/listados
            $validated['ncf'] = null;
            $validated['impuestos'] = 0;
            $validated['total'] = $subtotal;
        }

        $validated['aplicar_impuesto'] = $aplicarImpuesto;

        DB::transaction(function () use ($factura, $validated) {
            $factura->update($validated);
        });

        return redirect()->route('facturas.show', $factura)
            ->with('success', 'Factura actualizada exitosamente.');
    }

    private function obtenerRecibidoPor(Factura $factura): string
    {
        $reparacion = $factura->reparacion;
        if (!$reparacion) {
            return '';
        }
        $usuario = $reparacion->recepcionista;
        if (!$usuario) {
            $estadoRecibido = $reparacion->historialEstados()->where('estado', 'Recibido')->orderBy('created_at')->first();
            $usuario = $estadoRecibido?->usuario;
        }
        return $usuario ? trim(($usuario->firstname ?? '') . ' ' . ($usuario->lastname ?? '')) ?: ($usuario->username ?? '') : '';
    }

    private function obtenerPreparadoPor(Factura $factura): string
    {
        $reparacion = $factura->reparacion;
        if (!$reparacion) {
            return '';
        }
        $usuario = $reparacion->tecnicoCompleto ?? $reparacion->tecnico;
        if (!$usuario) {
            $estadoFinalizado = $reparacion->historialEstados()->whereIn('estado', ['Finalizado', 'Sin Reparación', 'Entregado'])->orderBy('created_at', 'desc')->first();
            $usuario = $estadoFinalizado?->usuario;
        }
        return $usuario ? trim(($usuario->firstname ?? '') . ' ' . ($usuario->lastname ?? '')) ?: ($usuario->username ?? '') : '';
    }
}
