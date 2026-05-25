<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacturaConfiguracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'mostrar_logo' => $this->boolean('mostrar_logo'),
            'mostrar_terminos' => $this->boolean('mostrar_terminos'),
        ]);
    }

    public function rules(): array
    {
        return [
            'empresa_nombre' => 'required|string|max:255',
            'empresa_cedula_rnc' => 'nullable|string|max:50',
            'empresa_direccion' => 'nullable|string',
            'empresa_telefono' => 'nullable|string|max:50',
            'empresa_email' => 'nullable|email|max:255',
            'empresa_website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'encabezado_factura' => 'nullable|string',
            'pie_factura' => 'nullable|string',
            'terminos_condiciones' => 'nullable|string',
            'impuesto_porcentaje' => 'nullable|numeric|min:0|max:100',
            'impuestos_activos' => 'nullable|boolean',
            'ncf_codigo' => 'nullable|string|max:50',
            'moneda' => 'required|string|max:10',
            'simbolo_moneda' => 'required|string|max:5',
            'mostrar_logo' => 'boolean',
            'mostrar_terminos' => 'boolean',
            'formato_numero_factura' => 'required|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'empresa_nombre.required' => 'El nombre de la empresa es obligatorio.',
            'impuesto_porcentaje.numeric' => 'El porcentaje de impuesto debe ser un número.',
            'impuesto_porcentaje.min' => 'El porcentaje de impuesto no puede ser negativo.',
            'impuesto_porcentaje.max' => 'El porcentaje de impuesto no puede ser mayor a 100.',
            'ncf_codigo.max' => 'El código NCF no puede exceder 50 caracteres.',
            'moneda.required' => 'La moneda es obligatoria.',
            'simbolo_moneda.required' => 'El símbolo de moneda es obligatorio.',
            'formato_numero_factura.required' => 'El formato del número de factura es obligatorio.',
            'logo.image' => 'El archivo debe ser una imagen.',
            'logo.max' => 'La imagen no puede ser mayor a 2MB.',
        ];
    }
}
