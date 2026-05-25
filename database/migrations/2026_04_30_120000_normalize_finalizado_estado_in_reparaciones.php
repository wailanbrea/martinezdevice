<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('reparaciones')
            ->where('estado', 'listo')
            ->update(['estado' => 'Finalizado']);

        DB::table('estados_reparacion')
            ->where('estado', 'listo')
            ->update(['estado' => 'Finalizado']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('reparaciones')
            ->where('estado', 'Finalizado')
            ->update(['estado' => 'listo']);

        DB::table('estados_reparacion')
            ->where('estado', 'Finalizado')
            ->update(['estado' => 'listo']);
    }
};
