<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reparacion;
use App\Models\Equipo;
use App\Models\EstadoReparacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GarantiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener reparaciones finalizadas/entregadas que puedan tener garantías
        $reparacionesOriginales = Reparacion::whereIn('estado', ['Finalizado', 'Entregado'])
            ->whereNotNull('fecha_finalizacion')
            ->with('equipo')
            ->orderBy('fecha_finalizacion', 'desc')
            ->limit(10)
            ->get();

        if ($reparacionesOriginales->isEmpty()) {
            $this->command->warn('No hay reparaciones finalizadas/entregadas para crear garantías. Primero debe haber reparaciones completadas.');
            return;
        }

        $this->command->info('Creando garantías de ejemplo...');

        $contador = 0;

        foreach ($reparacionesOriginales->take(6) as $index => $reparacionOriginal) {
            $equipoOriginal = $reparacionOriginal->equipo;
            
            // Crear un nuevo equipo para la garantía (simula que el cliente trae el mismo equipo de vuelta)
            $equipoGarantia = Equipo::create([
                'cliente_id' => $equipoOriginal->cliente_id,
                'tipo' => $equipoOriginal->tipo,
                'tipo_personalizado' => $equipoOriginal->tipo_personalizado,
                'marca' => $equipoOriginal->marca,
                'modelo' => $equipoOriginal->modelo,
                'numero_serie' => $equipoOriginal->numero_serie,
                'descripcion_problema' => 'Problema en garantía: ' . ($equipoOriginal->descripcion_problema ?? 'Revisión por garantía'),
                'estado' => 'recibido',
            ]);

            // Determinar fechas según el tipo de garantía
            $fechaFinalizacionOriginal = $reparacionOriginal->fecha_finalizacion;
            $periodoGarantia = [30, 60, 90][$index % 3]; // Rotar entre 30, 60, 90 días
            
            // Crear diferentes tipos de garantías
            if ($index < 2) {
                // Garantías VENCIDAS (finalizadas hace más de 30 días)
                $fechaIngreso = $fechaFinalizacionOriginal->copy()->addDays($periodoGarantia + 5); // 5 días después de vencer
                $fechaVencimiento = $fechaFinalizacionOriginal->copy()->addDays($periodoGarantia);
                $estado = 'Recibido';
            } elseif ($index < 4) {
                // Garantías POR VENCER (vencen en los próximos 7 días)
                $fechaVencimiento = Carbon::now()->addDays(rand(1, 7));
                $fechaIngreso = Carbon::now()->subDays(rand(1, 3));
                $estado = 'En Diagnóstico';
            } else {
                // Garantías NUEVAS (recién recibidas, vencen en más de 7 días)
                $fechaVencimiento = Carbon::now()->addDays(rand(15, 25));
                $fechaIngreso = Carbon::now()->subDays(rand(1, 5));
                $estado = 'Recibido';
            }

            // Crear la reparación en garantía
            $reparacionGarantia = Reparacion::create([
                'equipo_id' => $equipoGarantia->id,
                'tecnico_id' => $reparacionOriginal->tecnico_id,
                'recepcionista_id' => $reparacionOriginal->recepcionista_id ?? 1,
                'tipo_servicio' => 'reparacion',
                'es_garantia' => true,
                'periodo_garantia_dias' => $periodoGarantia,
                'fecha_vencimiento_garantia' => $fechaVencimiento,
                'reparacion_original_id' => $reparacionOriginal->id,
                'estado' => $estado,
                'fecha_ingreso' => $fechaIngreso,
                'fecha_prometida' => $fechaIngreso->copy()->addDays(7),
                'costo_diagnostico' => 0, // En garantía no se cobra diagnóstico
                'costo_piezas' => 0,
                'costo_mano_obra' => 0,
                'total_estimado' => 0,
            ]);

            // Crear estado inicial
            EstadoReparacion::create([
                'reparacion_id' => $reparacionGarantia->id,
                'estado' => $estado,
                'comentario' => 'Equipo recibido en garantía. Reparación original: ' . $reparacionOriginal->codigo_reparacion,
                'usuario_id' => $reparacionOriginal->recepcionista_id ?? 1,
            ]);

            $contador++;
            
            $tipoGarantia = $index < 2 ? 'VENCIDA' : ($index < 4 ? 'POR VENCER' : 'NUEVA');
            $this->command->info("✓ Garantía {$tipoGarantia} creada: {$reparacionGarantia->codigo_reparacion} (Original: {$reparacionOriginal->codigo_reparacion})");
        }

        $this->command->info("✓ Se crearon {$contador} garantías de ejemplo.");
    }
}
