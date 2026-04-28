<?php

namespace App\Http\Controllers;

use App\Http\Requests\FacturaConfiguracionRequest;
use App\Models\FacturaConfiguracion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacturaConfiguracionController extends Controller
{
    public function index()
    {
        $row = DB::connection()->table('factura_configuracion')->where('id', 1)->first();
        if (!$row) {
            $configuracion = FacturaConfiguracion::obtener();
        } else {
            $config = new FacturaConfiguracion();
            $config->setRawAttributes((array) $row);
            $config->syncOriginal();
            $config->exists = true;
            $configuracion = $config;
        }
        return view('facturas.configuracion', compact('configuracion'));
    }

    public function update(FacturaConfiguracionRequest $request)
    {
        Log::info('FacturaConfiguracion update iniciado', ['empresa_nombre' => $request->input('empresa_nombre')]);

        $validated = $request->validated();

        $configuracion = FacturaConfiguracion::find(1) ?? FacturaConfiguracion::obtener();

        if ($request->hasFile('logo')) {
            if ($configuracion->logo_path && Storage::disk('public')->exists($configuracion->logo_path)) {
                Storage::disk('public')->delete($configuracion->logo_path);
            }

            $ruta = $request->file('logo')->store('facturas/logo', 'public');
            $validated['logo_path'] = $ruta;
        }

        unset($validated['logo']);

        $camposOpcionales = ['empresa_cedula_rnc', 'empresa_direccion', 'empresa_telefono', 'empresa_email', 'empresa_website', 'encabezado_factura', 'pie_factura', 'terminos_condiciones', 'ncf_codigo'];
        foreach ($camposOpcionales as $campo) {
            if (isset($validated[$campo]) && $validated[$campo] === '') {
                $validated[$campo] = null;
            }
        }

        if (isset($validated['impuesto_porcentaje']) && ($validated['impuesto_porcentaje'] === '' || $validated['impuesto_porcentaje'] === null)) {
            $validated['impuesto_porcentaje'] = 18.00;
        }

        $columnasPermitidas = [
            'empresa_nombre', 'empresa_cedula_rnc', 'empresa_direccion', 'empresa_telefono',
            'empresa_email', 'empresa_website', 'logo_path', 'encabezado_factura', 'pie_factura',
            'terminos_condiciones', 'impuesto_porcentaje', 'ncf_codigo', 'moneda', 'simbolo_moneda',
            'mostrar_logo', 'mostrar_terminos', 'formato_numero_factura'
        ];
        $datosUpdate = array_intersect_key($validated, array_flip($columnasPermitidas));
        $datosUpdate['updated_at'] = now();

        $exists = DB::connection()->table('factura_configuracion')->where('id', 1)->exists();
        if ($exists) {
            DB::connection()->table('factura_configuracion')->where('id', 1)->update($datosUpdate);
        } else {
            $datosUpdate['id'] = 1;
            $datosUpdate['created_at'] = now();
            DB::connection()->table('factura_configuracion')->insert($datosUpdate);
        }

        Log::info('FacturaConfiguracion update completado', ['empresa_nombre' => $datosUpdate['empresa_nombre'] ?? '']);

        return redirect()->route('facturas.configuracion')
            ->with('success', 'Configuración de facturas actualizada exitosamente');
    }
}
