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
        // Para SQLite, necesitamos recrear la columna
        if (\DB::connection()->getDriverName() === 'sqlite') {
            // Primero eliminar la foreign key constraint (SQLite no soporta DROP FOREIGN KEY directamente)
            \DB::statement('CREATE TABLE estados_reparacion_temp AS SELECT * FROM estados_reparacion');
            \DB::statement('DROP TABLE estados_reparacion');
            \DB::statement('CREATE TABLE estados_reparacion (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                reparacion_id INTEGER NOT NULL,
                estado TEXT NOT NULL,
                comentario TEXT,
                usuario_id INTEGER,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                FOREIGN KEY (reparacion_id) REFERENCES reparaciones(id) ON DELETE CASCADE,
                FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
            )');
            \DB::statement('INSERT INTO estados_reparacion SELECT * FROM estados_reparacion_temp');
            \DB::statement('DROP TABLE estados_reparacion_temp');
        } else {
            Schema::table('estados_reparacion', function (Blueprint $table) {
                $table->foreignId('usuario_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\DB::connection()->getDriverName() === 'sqlite') {
            \DB::statement('CREATE TABLE estados_reparacion_temp AS SELECT * FROM estados_reparacion');
            \DB::statement('DROP TABLE estados_reparacion');
            \DB::statement('CREATE TABLE estados_reparacion (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                reparacion_id INTEGER NOT NULL,
                estado TEXT NOT NULL,
                comentario TEXT,
                usuario_id INTEGER NOT NULL,
                created_at TIMESTAMP,
                updated_at TIMESTAMP,
                FOREIGN KEY (reparacion_id) REFERENCES reparaciones(id) ON DELETE CASCADE,
                FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE CASCADE
            )');
            \DB::statement('INSERT INTO estados_reparacion SELECT * FROM estados_reparacion_temp WHERE usuario_id IS NOT NULL');
            \DB::statement('DROP TABLE estados_reparacion_temp');
        } else {
            Schema::table('estados_reparacion', function (Blueprint $table) {
                $table->foreignId('usuario_id')->nullable(false)->change();
            });
        }
    }
};
