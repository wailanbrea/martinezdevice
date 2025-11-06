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
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->string('tipo'); // PC, Laptop, GPU, Consola, etc.
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();
            $table->text('descripcion_falla');
            $table->enum('estado', ['recibido', 'diagnostico', 'reparacion', 'listo', 'entregado', 'garantia'])->default('recibido');
            $table->string('foto')->nullable(); // Ruta en storage/app/public/equipos
            $table->string('codigo_unico')->unique(); // UUID o hash único
            $table->text('codigo_qr')->nullable(); // QR code generado
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
