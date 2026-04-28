<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\Equipo;
use App\Models\Reparacion;
use App\Models\EquipoFoto;
use App\Models\Factura;
use App\Models\EstadoReparacion;
use App\Models\NotaReparacion;
use App\Models\Pieza;
use App\Models\Pago;

class LimpiarEquiposSeeder extends Seeder
{
    /**
     * Limpiar todos los equipos y sus datos relacionados
     * Mantiene clientes y usuarios intactos
     */
    public function run(): void
    {
        $this->command->info('🧹 Limpiando equipos y datos relacionados...');

        // Obtener driver de base de datos
        $driver = DB::connection()->getDriverName();
        
        // Desactivar foreign keys temporalmente (solo SQLite)
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
        }

        try {
            // Obtener todos los equipos antes de eliminar para limpiar archivos
            $equipos = Equipo::all();
            
            // Limpiar en orden inverso de dependencias
            $this->command->info('Eliminando pagos...');
            DB::table('pagos')->delete();
            
            $this->command->info('Eliminando facturas...');
            DB::table('facturas')->delete();
            
            $this->command->info('Eliminando notas de reparación...');
            DB::table('notas_reparacion')->delete();
            
            $this->command->info('Eliminando piezas...');
            DB::table('piezas')->delete();
            
            $this->command->info('Eliminando estados de reparación...');
            DB::table('estados_reparacion')->delete();
            
            $this->command->info('Eliminando reparaciones...');
            DB::table('reparaciones')->delete();
            
            $this->command->info('Eliminando fotos de equipos...');
            // Eliminar archivos físicos de fotos
            foreach ($equipos as $equipo) {
                $fotos = EquipoFoto::where('equipo_id', $equipo->id)->get();
                foreach ($fotos as $foto) {
                    if ($foto->ruta && Storage::exists($foto->ruta)) {
                        Storage::delete($foto->ruta);
                    }
                }
            }
            DB::table('equipo_fotos')->delete();
            
            $this->command->info('Eliminando equipos...');
            DB::table('equipos')->delete();
            
            // Resetear secuencias/auto_increment
            if ($driver === 'sqlite') {
                DB::statement('DELETE FROM sqlite_sequence WHERE name IN ("pagos", "facturas", "notas_reparacion", "piezas", "estados_reparacion", "reparaciones", "equipo_fotos", "equipos")');
                DB::statement('PRAGMA foreign_keys=ON');
            } else {
                // Para MySQL, resetear auto_increment
                try {
                    DB::statement('ALTER TABLE pagos AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE facturas AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE notas_reparacion AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE piezas AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE estados_reparacion AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE reparaciones AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE equipo_fotos AUTO_INCREMENT = 1');
                    DB::statement('ALTER TABLE equipos AUTO_INCREMENT = 1');
                } catch (\Exception $e) {
                    $this->command->warn('No se pudieron resetear los auto_increment (puede ser normal si las tablas están vacías)');
                }
            }
            
            // Limpiar caché del dashboard
            $this->command->info('Limpiando caché del dashboard...');
            Cache::forget('dashboard.stats');
            Cache::forget('dashboard.reparaciones_por_mes');
            Cache::forget('dashboard.siguiente_mantenimiento');
            Cache::forget('dashboard.siguiente_reparacion');
            Cache::forget('dashboard.siguiente_gpu');
            Cache::forget('dashboard.total_mantenimientos');
            Cache::forget('dashboard.total_reparaciones');
            Cache::forget('dashboard.total_gpus');
            Cache::forget('clientes.list');
            
            $this->command->info('✅ Limpieza completada exitosamente');
            $this->command->info('📊 Equipos eliminados: ' . $equipos->count());
            $this->command->info('💡 Los clientes y usuarios se mantienen intactos');
            
        } catch (\Exception $e) {
            $this->command->error('❌ Error durante la limpieza: ' . $e->getMessage());
            throw $e;
        } finally {
            // Reactivar foreign keys (solo SQLite)
            if ($driver === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=ON');
            }
        }
    }
}
