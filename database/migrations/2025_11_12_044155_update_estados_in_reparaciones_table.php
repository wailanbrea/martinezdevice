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
        $driver = \DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            // MySQL: modificar ENUM directamente
            \DB::statement("ALTER TABLE reparaciones MODIFY COLUMN estado ENUM('Recibido', 'En Diagnóstico', 'Esperando Aprobación', 'Aprobado', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'Entregado', 'Cancelado') DEFAULT 'Recibido'");
        } elseif ($driver === 'sqlite') {
            // SQLite: no soporta ENUM, pero podemos verificar que los valores sean válidos
            // En SQLite, el enum se maneja como string, así que solo necesitamos asegurar que los datos existentes sean válidos
            // No hacemos nada aquí ya que SQLite no tiene restricciones ENUM reales
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = \DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            \DB::statement("ALTER TABLE reparaciones MODIFY COLUMN estado ENUM('Recibido', 'En Diagnóstico', 'Esperando Pieza', 'En Proceso', 'Finalizado', 'Entregado', 'Cancelado') DEFAULT 'Recibido'");
        }
    }
};
