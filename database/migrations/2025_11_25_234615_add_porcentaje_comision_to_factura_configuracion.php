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
        Schema::table('factura_configuracion', function (Blueprint $table) {
            $table->decimal('porcentaje_comision', 5, 2)->nullable()->after('impuesto_porcentaje')->default(10.00)->comment('Porcentaje de comisión por defecto para técnicos (ej: 10.00 para 10%)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_configuracion', function (Blueprint $table) {
            $table->dropColumn('porcentaje_comision');
        });
    }
};
