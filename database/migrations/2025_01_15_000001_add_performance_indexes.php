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
        // Índices para tabla reparaciones
        Schema::table('reparaciones', function (Blueprint $table) {
            // Índice compuesto para consultas frecuentes de estado y fecha
            if (!$this->indexExists('reparaciones', 'reparaciones_estado_fecha_ingreso_index')) {
                $table->index(['estado', 'fecha_ingreso'], 'reparaciones_estado_fecha_ingreso_index');
            }
            
            // Índice para tipo_servicio (si existe la columna)
            if (Schema::hasColumn('reparaciones', 'tipo_servicio')) {
                if (!$this->indexExists('reparaciones', 'reparaciones_tipo_servicio_index')) {
                    $table->index('tipo_servicio', 'reparaciones_tipo_servicio_index');
                }
            }
            
            // Índice para fecha_finalizacion (usado en contabilidad)
            if (!$this->indexExists('reparaciones', 'reparaciones_fecha_finalizacion_index')) {
                $table->index('fecha_finalizacion', 'reparaciones_fecha_finalizacion_index');
            }
            
            // Índice para técnico_id
            if (!$this->indexExists('reparaciones', 'reparaciones_tecnico_id_index')) {
                $table->index('tecnico_id', 'reparaciones_tecnico_id_index');
            }
        });

        // Índices para tabla equipos
        Schema::table('equipos', function (Blueprint $table) {
            // Índice para tipo (usado en filtros)
            if (!$this->indexExists('equipos', 'equipos_tipo_index')) {
                $table->index('tipo', 'equipos_tipo_index');
            }
            
            // Índice para estado (si existe la columna)
            if (Schema::hasColumn('equipos', 'estado')) {
                if (!$this->indexExists('equipos', 'equipos_estado_index')) {
                    $table->index('estado', 'equipos_estado_index');
                }
            }
            
            // Índice para número de serie (búsquedas)
            if (!$this->indexExists('equipos', 'equipos_numero_serie_index')) {
                $table->index('numero_serie', 'equipos_numero_serie_index');
            }
        });

        // Índices para tabla clientes
        Schema::table('clientes', function (Blueprint $table) {
            // Índice para teléfono (búsquedas y validaciones)
            if (!$this->indexExists('clientes', 'clientes_telefono_index')) {
                $table->index('telefono', 'clientes_telefono_index');
            }
            
            // Índice para email (búsquedas)
            if (!$this->indexExists('clientes', 'clientes_email_index')) {
                $table->index('email', 'clientes_email_index');
            }
        });

        // Índices para tabla estados_reparacion
        Schema::table('estados_reparacion', function (Blueprint $table) {
            // Índice compuesto para consultas de historial
            if (!$this->indexExists('estados_reparacion', 'estados_reparacion_reparacion_created_index')) {
                $table->index(['reparacion_id', 'created_at'], 'estados_reparacion_reparacion_created_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropIndex('reparaciones_estado_fecha_ingreso_index');
            if (Schema::hasColumn('reparaciones', 'tipo_servicio')) {
                $table->dropIndex('reparaciones_tipo_servicio_index');
            }
            $table->dropIndex('reparaciones_fecha_finalizacion_index');
            $table->dropIndex('reparaciones_tecnico_id_index');
        });

        Schema::table('equipos', function (Blueprint $table) {
            $table->dropIndex('equipos_tipo_index');
            if (Schema::hasColumn('equipos', 'estado')) {
                $table->dropIndex('equipos_estado_index');
            }
            $table->dropIndex('equipos_numero_serie_index');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('clientes_telefono_index');
            $table->dropIndex('clientes_email_index');
        });

        Schema::table('estados_reparacion', function (Blueprint $table) {
            $table->dropIndex('estados_reparacion_reparacion_created_index');
        });
    }

    /**
     * Verificar si un índice existe
     */
    private function indexExists(string $table, string $index): bool
    {
        $connection = Schema::getConnection();
        $databaseName = $connection->getDatabaseName();
        
        if ($connection->getDriverName() === 'mysql') {
            $result = $connection->select(
                "SELECT COUNT(*) as count FROM information_schema.statistics 
                 WHERE table_schema = ? AND table_name = ? AND index_name = ?",
                [$databaseName, $table, $index]
            );
            return $result[0]->count > 0;
        } else {
            // Para SQLite, intentar crear y capturar error
            try {
                $connection->statement("CREATE INDEX IF NOT EXISTS {$index} ON {$table}(id)");
                $connection->statement("DROP INDEX IF EXISTS {$index}");
                return false;
            } catch (\Exception $e) {
                return true;
            }
        }
    }
};

