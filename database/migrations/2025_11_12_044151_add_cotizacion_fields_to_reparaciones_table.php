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
            $table->enum('tipo_servicio', ['reparacion', 'mantenimiento'])->default('reparacion')->after('estado');
            $table->decimal('precio_cotizado', 10, 2)->nullable()->after('total_estimado');
            $table->boolean('cliente_aprobado')->default(false)->after('precio_cotizado');
            $table->timestamp('fecha_cotizacion')->nullable()->after('cliente_aprobado');
            $table->timestamp('fecha_aprobacion')->nullable()->after('fecha_cotizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropColumn(['tipo_servicio', 'precio_cotizado', 'cliente_aprobado', 'fecha_cotizacion', 'fecha_aprobacion']);
        });
    }
};
