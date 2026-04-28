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
        Schema::table('equipos', function (Blueprint $table) {
            $table->uuid('codigo_unico')->unique()->after('id');
            $table->string('foto')->nullable()->after('descripcion_problema');
            $table->text('codigo_qr')->nullable()->after('foto');
            $table->enum('estado', ['recibido', 'diagnostico', 'reparacion', 'listo', 'entregado', 'garantia'])->default('recibido')->after('codigo_qr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropColumn(['codigo_unico', 'foto', 'codigo_qr', 'estado']);
        });
    }
};
