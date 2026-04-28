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
        // Para SQLite, necesitamos recrear la columna
        if (\DB::connection()->getDriverName() === 'sqlite') {
            \DB::statement('ALTER TABLE reparaciones ADD COLUMN cliente_aprobado_temp BOOLEAN DEFAULT NULL');
            \DB::statement('UPDATE reparaciones SET cliente_aprobado_temp = cliente_aprobado');
            \DB::statement('ALTER TABLE reparaciones DROP COLUMN cliente_aprobado');
            \DB::statement('ALTER TABLE reparaciones RENAME COLUMN cliente_aprobado_temp TO cliente_aprobado');
        } else {
            Schema::table('reparaciones', function (Blueprint $table) {
                $table->boolean('cliente_aprobado')->nullable()->default(null)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\DB::connection()->getDriverName() === 'sqlite') {
            \DB::statement('ALTER TABLE reparaciones ADD COLUMN cliente_aprobado_temp BOOLEAN DEFAULT 0');
            \DB::statement('UPDATE reparaciones SET cliente_aprobado_temp = COALESCE(cliente_aprobado, 0)');
            \DB::statement('ALTER TABLE reparaciones DROP COLUMN cliente_aprobado');
            \DB::statement('ALTER TABLE reparaciones RENAME COLUMN cliente_aprobado_temp TO cliente_aprobado');
        } else {
            Schema::table('reparaciones', function (Blueprint $table) {
                $table->boolean('cliente_aprobado')->default(false)->change();
            });
        }
    }
};
