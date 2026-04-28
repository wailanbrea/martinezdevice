<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Guarda si se aplica impuesto a la cotización (para mostrar correctamente en consulta pública)
     */
    public function up(): void
    {
        if (!Schema::hasTable('reparaciones') || Schema::hasColumn('reparaciones', 'aplicar_impuesto_cotizacion')) {
            return;
        }

        Schema::table('reparaciones', function (Blueprint $table) {
            $table->boolean('aplicar_impuesto_cotizacion')->default(true)->after('descripcion_cotizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('reparaciones') || !Schema::hasColumn('reparaciones', 'aplicar_impuesto_cotizacion')) {
            return;
        }

        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropColumn('aplicar_impuesto_cotizacion');
        });
    }
};
