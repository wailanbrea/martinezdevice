<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('factura_configuracion') && !Schema::hasColumn('factura_configuracion', 'impuestos_activos')) {
            Schema::table('factura_configuracion', function (Blueprint $table) {
                $table->boolean('impuestos_activos')->default(true)->after('impuesto_porcentaje');
            });
        }

        if (Schema::hasTable('factura_configuracion')) {
            DB::table('factura_configuracion')
                ->whereNull('impuestos_activos')
                ->update(['impuestos_activos' => true]);
        }

        if (Schema::hasTable('reparaciones')) {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver === 'mysql') {
                DB::statement(
                    "ALTER TABLE reparaciones MODIFY COLUMN estado ENUM(" .
                    "'Recibido', 'En Diagnóstico', 'Pendiente Revisión Admin', 'Esperando Aprobación', " .
                    "'Aprobado', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'Sin ReparaciÃ³n', 'Entregado', 'Cancelado'" .
                    ") DEFAULT 'Recibido'"
                );
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('factura_configuracion') && Schema::hasColumn('factura_configuracion', 'impuestos_activos')) {
            Schema::table('factura_configuracion', function (Blueprint $table) {
                $table->dropColumn('impuestos_activos');
            });
        }

        if (Schema::hasTable('reparaciones')) {
            $driver = Schema::getConnection()->getDriverName();

            if ($driver === 'mysql') {
                DB::statement(
                    "ALTER TABLE reparaciones MODIFY COLUMN estado ENUM(" .
                    "'Recibido', 'En Diagnóstico', 'Pendiente Revisión Admin', 'Esperando Aprobación', " .
                    "'Aprobado', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'Entregado', 'Cancelado'" .
                    ") DEFAULT 'Recibido'"
                );
            }
        }
    }
};
