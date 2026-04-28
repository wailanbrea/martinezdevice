<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sistema_configuracion', function (Blueprint $table) {
            $table->decimal('porcentaje_comision', 5, 2)->nullable()->after('simbolo_moneda')->default(10.00)->comment('Porcentaje de comisión por defecto para técnicos (ej: 10.00 para 10%)');
        });

        // Actualizar el registro existente con el valor por defecto
        DB::table('sistema_configuracion')->where('id', 1)->update([
            'porcentaje_comision' => 10.00
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sistema_configuracion', function (Blueprint $table) {
            $table->dropColumn('porcentaje_comision');
        });
    }
};
