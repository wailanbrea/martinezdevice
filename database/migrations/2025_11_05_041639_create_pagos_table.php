<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_id')->constrained()->onDelete('cascade');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo_pago', ['efectivo', 'transferencia', 'tarjeta']);
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
