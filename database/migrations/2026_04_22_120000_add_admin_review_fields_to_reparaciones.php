<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->foreignId('cotizacion_revisada_por')
                ->nullable()
                ->after('aplicar_impuesto_cotizacion')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('cotizacion_revisada_at')
                ->nullable()
                ->after('cotizacion_revisada_por');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE reparaciones MODIFY COLUMN estado ENUM(" .
                "'Recibido', 'En Diagnóstico', 'Pendiente Revisión Admin', 'Esperando Aprobación', " .
                "'Aprobado', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'Entregado', 'Cancelado'" .
                ") DEFAULT 'Recibido'"
            );
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE reparaciones MODIFY COLUMN estado ENUM(" .
                "'Recibido', 'En Diagnóstico', 'Esperando Aprobación', 'Aprobado', " .
                "'Esperando Pieza', 'En Proceso', 'Finalizado', 'Entregado', 'Cancelado'" .
                ") DEFAULT 'Recibido'"
            );
        }

        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cotizacion_revisada_por');
            $table->dropColumn('cotizacion_revisada_at');
        });
    }
};
