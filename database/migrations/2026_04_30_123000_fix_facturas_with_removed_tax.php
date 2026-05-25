<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $facturas = DB::table('facturas')
            ->join('reparaciones', 'facturas.reparacion_id', '=', 'reparaciones.id')
            ->where('reparaciones.aplicar_impuesto_cotizacion', false)
            ->where('facturas.aplicar_impuesto', true)
            ->select('facturas.id', 'facturas.subtotal')
            ->get();

        foreach ($facturas as $factura) {
            DB::table('facturas')
                ->where('id', $factura->id)
                ->update([
                    'aplicar_impuesto' => false,
                    'ncf' => null,
                    'impuestos' => 0,
                    'total' => $factura->subtotal,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No se revierte porque no es posible inferir de forma segura el impuesto original.
    }
};
