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
        Schema::create('sistema_configuracion', function (Blueprint $table) {
            $table->id();
            $table->decimal('costo_diagnostico', 10, 2)->default(50.00)->comment('Costo por defecto del diagnostico');
            $table->decimal('costo_diagnostico_mantenimiento', 10, 2)->default(0.00)->comment('Costo de diagnostico para mantenimientos (generalmente 0)');
            $table->string('moneda', 10)->default('DOP')->comment('Moneda del sistema');
            $table->string('simbolo_moneda', 5)->default('$')->comment('Simbolo de la moneda');
            $table->string('ncf_codigo', 50)->nullable()->comment('Prefijo NCF por defecto para facturas');
            $table->boolean('imprimir_etiqueta_auto')->default(true)->comment('Imprimir etiqueta automaticamente al registrar');
            $table->timestamps();
        });

        // Insertar registro inicial
        DB::table('sistema_configuracion')->insert([
            'costo_diagnostico' => 50.00,
            'costo_diagnostico_mantenimiento' => 0.00,
            'moneda' => 'DOP',
            'simbolo_moneda' => '$',
            'ncf_codigo' => null,
            'imprimir_etiqueta_auto' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_configuracion');
    }
};
