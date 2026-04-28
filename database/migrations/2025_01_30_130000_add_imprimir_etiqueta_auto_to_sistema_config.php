<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sistema_configuracion') || Schema::hasColumn('sistema_configuracion', 'imprimir_etiqueta_auto')) {
            return;
        }

        Schema::table('sistema_configuracion', function (Blueprint $table) {
            $table->boolean('imprimir_etiqueta_auto')->default(true)->after('ncf_codigo');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('sistema_configuracion') || !Schema::hasColumn('sistema_configuracion', 'imprimir_etiqueta_auto')) {
            return;
        }

        Schema::table('sistema_configuracion', function (Blueprint $table) {
            $table->dropColumn('imprimir_etiqueta_auto');
        });
    }
};
