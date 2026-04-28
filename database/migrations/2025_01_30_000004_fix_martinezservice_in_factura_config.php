<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Corregir "MartinezService" a "Martinez Devices" en factura_configuracion.
     * Solo actualiza si el valor actual es MartinezService (no borra datos).
     */
    public function up(): void
    {
        if (!Schema::hasTable('factura_configuracion')) {
            return;
        }

        DB::table('factura_configuracion')
            ->where('id', 1)
            ->where('empresa_nombre', 'MartinezService')
            ->update([
                'empresa_nombre' => 'Martinez Devices',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertir - mantener Martinez Devices
    }
};
