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
        Schema::create('garantias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('reparacion_id')->nullable()->constrained('reparaciones')->onDelete('set null');
            $table->date('fecha_inicio'); // Fecha de entrega
            $table->date('fecha_vencimiento'); // Calculada automáticamente
            $table->integer('duracion_dias')->default(30); // Configurable, por defecto 30 días
            $table->text('descripcion')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garantias');
    }
};
