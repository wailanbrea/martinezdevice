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
            $table->boolean('es_garantia')->default(false)->after('tipo_servicio');
            $table->integer('periodo_garantia_dias')->nullable()->after('es_garantia')->comment('Período de garantía en días (ej: 30, 60, 90)');
            $table->date('fecha_vencimiento_garantia')->nullable()->after('periodo_garantia_dias')->comment('Fecha de vencimiento de la garantía');
            $table->text('reparacion_original_id')->nullable()->after('fecha_vencimiento_garantia')->comment('ID de la reparación original que generó esta garantía');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropColumn(['es_garantia', 'periodo_garantia_dias', 'fecha_vencimiento_garantia', 'reparacion_original_id']);
        });
    }
};
