<?php

namespace App\Http\Controllers;

use App\Models\SistemaConfiguracion;
use Illuminate\Http\Request;

class SistemaConfiguracionController extends Controller
{
    public function index()
    {
        $configuracion = SistemaConfiguracion::obtener();
        return view('sistema.configuracion', compact('configuracion'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'costo_diagnostico' => 'required|numeric|min:0',
            'costo_diagnostico_mantenimiento' => 'required|numeric|min:0',
            'moneda' => 'required|string|max:10',
            'simbolo_moneda' => 'required|string|max:5',
            'porcentaje_comision' => 'required|numeric|min:0|max:100',
            'ncf_codigo' => 'nullable|string|max:50',
            'imprimir_etiqueta_auto' => 'nullable|boolean',
        ], [
            'costo_diagnostico.required' => 'El costo de diagnóstico es obligatorio.',
            'costo_diagnostico.numeric' => 'El costo de diagnóstico debe ser un número.',
            'costo_diagnostico.min' => 'El costo de diagnóstico no puede ser negativo.',
            'costo_diagnostico_mantenimiento.required' => 'El costo de diagnóstico para mantenimientos es obligatorio.',
            'costo_diagnostico_mantenimiento.numeric' => 'El costo de diagnóstico para mantenimientos debe ser un número.',
            'costo_diagnostico_mantenimiento.min' => 'El costo de diagnóstico para mantenimientos no puede ser negativo.',
            'moneda.required' => 'La moneda es obligatoria.',
            'simbolo_moneda.required' => 'El símbolo de moneda es obligatorio.',
            'porcentaje_comision.required' => 'El porcentaje de comisión es obligatorio.',
            'porcentaje_comision.numeric' => 'El porcentaje de comisión debe ser un número.',
            'porcentaje_comision.min' => 'El porcentaje de comisión no puede ser negativo.',
            'porcentaje_comision.max' => 'El porcentaje de comisión no puede ser mayor a 100.',
            'ncf_codigo.max' => 'El código NCF no puede exceder 50 caracteres.',
        ]);

        $configuracion = SistemaConfiguracion::obtener();
        if (isset($validated['ncf_codigo']) && trim($validated['ncf_codigo']) === '') {
            $validated['ncf_codigo'] = null;
        }
        $validated['imprimir_etiqueta_auto'] = $request->boolean('imprimir_etiqueta_auto');
        $configuracion->update($validated);

        return redirect()->route('sistema.configuracion')
            ->with('success', 'Configuración del sistema actualizada exitosamente');
    }

    public function probarImpresion()
    {
        return view('sistema.probar-impresion');
    }
}
