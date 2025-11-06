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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura')->unique(); // Autonumérico
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('equipo_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('reparacion_id')->nullable()->unique()->constrained('reparaciones')->onDelete('set null');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('impuestos', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('forma_pago', ['efectivo', 'transferencia', 'tarjeta'])->default('efectivo');
            $table->date('fecha_emision');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
