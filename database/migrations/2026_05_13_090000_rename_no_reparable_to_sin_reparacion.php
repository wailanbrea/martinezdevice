<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reparaciones')) {
            return;
        }

        DB::table('estados_reparacion')
            ->where('estado', 'No Reparable')
            ->update(['estado' => 'Sin Reparación']);

        DB::table('reparaciones')
            ->where('estado', 'No Reparable')
            ->update(['estado' => 'Sin Reparación']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE reparaciones MODIFY COLUMN estado ENUM(" .
                "'Recibido', 'En Diagnóstico', 'Pendiente Revisión Admin', 'Esperando Aprobación', " .
                "'Aprobado', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'Sin Reparación', 'Entregado', 'Cancelado'" .
                ") DEFAULT 'Recibido'"
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('reparaciones')) {
            return;
        }

        DB::table('estados_reparacion')
            ->where('estado', 'Sin Reparación')
            ->update(['estado' => 'No Reparable']);

        DB::table('reparaciones')
            ->where('estado', 'Sin Reparación')
            ->update(['estado' => 'No Reparable']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE reparaciones MODIFY COLUMN estado ENUM(" .
                "'Recibido', 'En Diagnóstico', 'Pendiente Revisión Admin', 'Esperando Aprobación', " .
                "'Aprobado', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'No Reparable', 'Entregado', 'Cancelado'" .
                ") DEFAULT 'Recibido'"
            );
        }
    }
};
