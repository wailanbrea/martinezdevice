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
        Schema::table('reparaciones', function (Blueprint $table) {
            // Técnico que completó el trabajo (para calcular comisiones)
            $table->unsignedBigInteger('tecnico_completo_id')->nullable()->after('tecnico_id');
            $table->foreign('tecnico_completo_id')->references('id')->on('users')->onDelete('set null');
            
            // Porcentaje de comisión para el técnico que completó
            $table->decimal('porcentaje_comision', 5, 2)->nullable()->after('tecnico_completo_id')->comment('Porcentaje de comisión (ej: 10.50 para 10.5%)');
            
            // Monto de comisión calculado
            $table->decimal('monto_comision', 10, 2)->nullable()->after('porcentaje_comision')->comment('Monto de comisión calculado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropForeign(['tecnico_completo_id']);
            $table->dropColumn(['tecnico_completo_id', 'porcentaje_comision', 'monto_comision']);
        });
    }
};
