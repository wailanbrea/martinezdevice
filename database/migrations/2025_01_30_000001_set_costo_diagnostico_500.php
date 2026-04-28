<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Diagnóstico pasa a 500 por defecto (antes 50).
     */
    public function up(): void
    {
        if (!Schema::hasTable('sistema_configuracion') || !Schema::hasColumn('sistema_configuracion', 'costo_diagnostico')) {
            return;
        }

        DB::table('sistema_configuracion')
            ->where('id', 1)
            ->update(['costo_diagnostico' => 500.00]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('sistema_configuracion') || !Schema::hasColumn('sistema_configuracion', 'costo_diagnostico')) {
            return;
        }

        DB::table('sistema_configuracion')
            ->where('id', 1)
            ->update(['costo_diagnostico' => 50.00]);
    }
};
