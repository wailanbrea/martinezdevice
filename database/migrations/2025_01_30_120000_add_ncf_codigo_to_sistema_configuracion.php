<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sistema_configuracion') || Schema::hasColumn('sistema_configuracion', 'ncf_codigo')) {
            return;
        }

        Schema::table('sistema_configuracion', function (Blueprint $table) {
            $table->string('ncf_codigo', 50)->nullable()->after('porcentaje_comision');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('sistema_configuracion') || !Schema::hasColumn('sistema_configuracion', 'ncf_codigo')) {
            return;
        }

        Schema::table('sistema_configuracion', function (Blueprint $table) {
            $table->dropColumn('ncf_codigo');
        });
    }
};
