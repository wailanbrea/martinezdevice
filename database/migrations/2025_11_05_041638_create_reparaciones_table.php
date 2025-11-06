<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('tecnico_id')->constrained('users')->onDelete('cascade');
            $table->text('diagnostico')->nullable();
            $table->json('piezas_reemplazadas')->nullable(); // Array de piezas
            $table->text('observaciones')->nullable();
            $table->decimal('costo_mano_obra', 10, 2)->default(0);
            $table->decimal('costo_piezas', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_finalizacion')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reparaciones');
    }
};
