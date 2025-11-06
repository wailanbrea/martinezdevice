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
        Schema::create('historial_estados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->enum('estado_anterior', ['recibido', 'diagnostico', 'reparacion', 'listo', 'entregado', 'garantia'])->nullable();
            $table->enum('estado_nuevo', ['recibido', 'diagnostico', 'reparacion', 'listo', 'entregado', 'garantia']);
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_estados');
    }
};
