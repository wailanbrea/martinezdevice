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
            $table->text('descripcion_cotizacion')->nullable()->after('precio_cotizado');
            $table->boolean('aplicar_impuesto_cotizacion')->default(true)->after('descripcion_cotizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropColumn(['descripcion_cotizacion', 'aplicar_impuesto_cotizacion']);
        });
    }
};
