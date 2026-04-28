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
        Schema::create('factura_configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('empresa_nombre')->default('Martinez Devices');
            $table->string('empresa_cedula_rnc')->nullable();
            $table->text('empresa_direccion')->nullable();
            $table->string('empresa_telefono')->nullable();
            $table->string('empresa_email')->nullable();
            $table->string('empresa_website')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('encabezado_factura')->nullable();
            $table->text('pie_factura')->nullable();
            $table->text('terminos_condiciones')->nullable();
            $table->decimal('impuesto_porcentaje', 5, 2)->default(18.00);
            $table->string('moneda', 10)->default('DOP');
            $table->string('simbolo_moneda', 5)->default('$');
            $table->boolean('mostrar_logo')->default(true);
            $table->boolean('mostrar_terminos')->default(true);
            $table->string('formato_numero_factura')->default('FAC-{YEAR}-{NUM}');
            $table->timestamps();
        });

        // Insertar configuración por defecto
        DB::table('factura_configuracion')->insert([
            'empresa_nombre' => 'Martinez Devices',
            'impuesto_porcentaje' => 18.00,
            'moneda' => 'DOP',
            'simbolo_moneda' => '$',
            'formato_numero_factura' => 'FAC-{YEAR}-{NUM}',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factura_configuracion');
    }
};
