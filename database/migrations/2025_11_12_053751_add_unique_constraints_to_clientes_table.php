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
        // Agregar unique a email y teléfono solo si no hay duplicados
        Schema::table('clientes', function (Blueprint $table) {
            // Primero verificar si hay duplicados antes de agregar unique
            $duplicadosEmail = \DB::table('clientes')
                ->select('email', \DB::raw('count(*) as total'))
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->groupBy('email')
                ->having('total', '>', 1)
                ->count();
            
            $duplicadosTelefono = \DB::table('clientes')
                ->select('telefono', \DB::raw('count(*) as total'))
                ->groupBy('telefono')
                ->having('total', '>', 1)
                ->count();

            // Solo agregar unique si no hay duplicados
            if ($duplicadosEmail == 0) {
                $table->unique('email');
            }
            
            if ($duplicadosTelefono == 0) {
                $table->unique('telefono');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['telefono']);
        });
    }
};
