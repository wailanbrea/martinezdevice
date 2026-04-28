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
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_reparacion')->unique();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('tecnico_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('estado', ['Recibido', 'En Diagnóstico', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'Entregado', 'Cancelado'])->default('Recibido');
            $table->date('fecha_ingreso');
            $table->date('fecha_prometida')->nullable();
            $table->date('fecha_finalizacion')->nullable();
            $table->decimal('costo_diagnostico', 10, 2)->default(0);
            $table->decimal('costo_piezas', 10, 2)->default(0);
            $table->decimal('costo_mano_obra', 10, 2)->default(0);
            $table->decimal('total_estimado', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reparaciones');
    }
};
