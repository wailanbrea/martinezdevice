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
        $driver = Schema::getConnection()->getDriverName();
        
        // Índices para la tabla reparaciones (consultas más frecuentes)
        try {
            Schema::table('reparaciones', function (Blueprint $table) use ($driver) {
                // Índice compuesto para consultas de dashboard (tipo_servicio + estado + fecha_ingreso)
                try {
                    $table->index(['tipo_servicio', 'estado', 'fecha_ingreso'], 'idx_tipo_estado_fecha');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
                
                // Índice para garantías
                try {
                    $table->index(['es_garantia', 'fecha_vencimiento_garantia'], 'idx_garantia_vencimiento');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
                
                // Índice para estado
                try {
                    $table->index('estado', 'idx_estado');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
                
                // Índice para fecha_ingreso (usado en ordenamiento)
                try {
                    $table->index('fecha_ingreso', 'idx_fecha_ingreso');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
            });
        } catch (\Exception $e) {
            // Continuar si hay algún error
        }

        // Índices para la tabla equipos
        try {
            Schema::table('equipos', function (Blueprint $table) {
                // Índice para tipo (usado en filtros de GPU)
                try {
                    $table->index('tipo', 'idx_tipo');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
                
                // Índice para cliente_id (usado en relaciones)
                try {
                    $table->index('cliente_id', 'idx_cliente_id');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
                
                // Índice para estado
                try {
                    $table->index('estado', 'idx_estado');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
            });
        } catch (\Exception $e) {
            // Continuar si hay algún error
        }

        // Índices para la tabla clientes
        try {
            Schema::table('clientes', function (Blueprint $table) {
                // Índice para búsquedas por nombre
                try {
                    $table->index('nombre', 'idx_nombre');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
                
                // Índice para email (usado en búsquedas)
                try {
                    $table->index('email', 'idx_email');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
            });
        } catch (\Exception $e) {
            // Continuar si hay algún error
        }

        // Índices para la tabla estado_reparaciones
        try {
            Schema::table('estado_reparaciones', function (Blueprint $table) {
                // Índice para reparacion_id (usado en relaciones)
                try {
                    $table->index('reparacion_id', 'idx_reparacion_id');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
                
                // Índice compuesto para actividad reciente
                try {
                    $table->index(['reparacion_id', 'created_at'], 'idx_reparacion_created');
                } catch (\Exception $e) {
                    // Índice ya existe, continuar
                }
            });
        } catch (\Exception $e) {
            // Continuar si hay algún error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            $table->dropIndex('idx_tipo_estado_fecha');
            $table->dropIndex('idx_garantia_vencimiento');
            $table->dropIndex('idx_estado');
            $table->dropIndex('idx_fecha_ingreso');
        });

        Schema::table('equipos', function (Blueprint $table) {
            $table->dropIndex('idx_tipo');
            $table->dropIndex('idx_cliente_id');
            $table->dropIndex('idx_estado');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex('idx_nombre');
            $table->dropIndex('idx_email');
        });

        Schema::table('estado_reparaciones', function (Blueprint $table) {
            $table->dropIndex('idx_reparacion_id');
            $table->dropIndex('idx_reparacion_created');
        });
    }

};
