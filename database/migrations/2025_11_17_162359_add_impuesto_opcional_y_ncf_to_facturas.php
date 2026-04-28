<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Agregar campos a facturas
        Schema::table('facturas', function (Blueprint $table) {
            $table->boolean('aplicar_impuesto')->default(false)->after('subtotal');
            $table->string('ncf')->nullable()->after('aplicar_impuesto');
        });

        // Agregar campo NCF código a configuración
        Schema::table('factura_configuracion', function (Blueprint $table) {
            $table->string('ncf_codigo')->nullable()->after('impuesto_porcentaje');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['aplicar_impuesto', 'ncf']);
        });

        Schema::table('factura_configuracion', function (Blueprint $table) {
            $table->dropColumn('ncf_codigo');
        });
    }
};
