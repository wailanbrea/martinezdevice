<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Reparacion;
use App\Models\Pieza;
use App\Models\NotaReparacion;
use App\Models\EstadoReparacion;
use App\Models\User;
use Carbon\Carbon;

class ReparacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tecnicos = User::whereHas('roles', function($query) {
            $query->where('slug', 'tecnico');
        })->get();

        // Reparación 1 - PC Gamer de Ana García
        $rep1 = Reparacion::create([
            'equipo_id' => 1,
            'tecnico_id' => $tecnicos->first()->id,
            'estado' => 'En Proceso',
            'fecha_ingreso' => Carbon::now()->subDays(5),
            'fecha_prometida' => Carbon::now()->addDays(2),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 120.00,
            'costo_mano_obra' => 85.00,
            'total_estimado' => 255.00,
        ]);

        // Agregar piezas a la reparación 1
        Pieza::create([
            'reparacion_id' => $rep1->id,
            'nombre' => 'Módulo RAM DDR4 8GB',
            'descripcion' => 'Kingston HyperX Fury',
            'cantidad' => 2,
            'precio_unitario' => 45.00,
            'precio_total' => 90.00,
        ]);

        Pieza::create([
            'reparacion_id' => $rep1->id,
            'nombre' => 'Pasta Térmica',
            'descripcion' => 'Arctic MX-4',
            'cantidad' => 1,
            'precio_unitario' => 15.00,
            'precio_total' => 15.00,
        ]);

        // Agregar notas
        NotaReparacion::create([
            'reparacion_id' => $rep1->id,
            'usuario_id' => $tecnicos->first()->id,
            'nota' => 'Diagnóstico inicial completado. Se detectó falla en módulos RAM. Se procede al reemplazo.',
        ]);

        NotaReparacion::create([
            'reparacion_id' => $rep1->id,
            'usuario_id' => $tecnicos->first()->id,
            'nota' => 'RAM reemplazada, sistema estable. Realizando pruebas de estrés.',
        ]);

        // Agregar historial de estados
        EstadoReparacion::create([
            'reparacion_id' => $rep1->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido y registrado en el sistema',
            'usuario_id' => $tecnicos->first()->id,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $rep1->id,
            'estado' => 'En Diagnóstico',
            'comentario' => 'Iniciando proceso de diagnóstico',
            'usuario_id' => $tecnicos->first()->id,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $rep1->id,
            'estado' => 'En Proceso',
            'comentario' => 'Falla identificada, procediendo con reparación',
            'usuario_id' => $tecnicos->first()->id,
        ]);

        // Reparación 2 - GPU de Juan Pérez
        $rep2 = Reparacion::create([
            'equipo_id' => 2,
            'tecnico_id' => $tecnicos->last()->id,
            'estado' => 'Esperando Pieza',
            'fecha_ingreso' => Carbon::now()->subDays(3),
            'fecha_prometida' => Carbon::now()->addDays(7),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 280.00,
            'costo_mano_obra' => 150.00,
            'total_estimado' => 480.00,
        ]);

        Pieza::create([
            'reparacion_id' => $rep2->id,
            'nombre' => 'Chip VRAM GDDR6X',
            'descripcion' => 'Módulos de memoria para RTX 3080',
            'cantidad' => 8,
            'precio_unitario' => 35.00,
            'precio_total' => 280.00,
        ]);

        NotaReparacion::create([
            'reparacion_id' => $rep2->id,
            'usuario_id' => $tecnicos->last()->id,
            'nota' => 'Diagnóstico completado. El problema es falla en chip de memoria VRAM. Se requiere reemplazo. Pieza solicitada al proveedor.',
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $rep2->id,
            'estado' => 'Recibido',
            'comentario' => 'GPU recibida para diagnóstico',
            'usuario_id' => $tecnicos->last()->id,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $rep2->id,
            'estado' => 'En Diagnóstico',
            'comentario' => 'Realizando pruebas de voltaje y temperatura',
            'usuario_id' => $tecnicos->last()->id,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $rep2->id,
            'estado' => 'Esperando Pieza',
            'comentario' => 'Chip VRAM pedido, llegada estimada en 5-7 días',
            'usuario_id' => $tecnicos->last()->id,
        ]);

        // Reparación 3 - Laptop HP de María
        $rep3 = Reparacion::create([
            'equipo_id' => 3,
            'tecnico_id' => $tecnicos->first()->id,
            'estado' => 'Finalizado',
            'fecha_ingreso' => Carbon::now()->subDays(10),
            'fecha_prometida' => Carbon::now()->subDays(3),
            'fecha_finalizacion' => Carbon::now()->subDays(1),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 180.00,
            'costo_mano_obra' => 100.00,
            'total_estimado' => 330.00,
        ]);

        Pieza::create([
            'reparacion_id' => $rep3->id,
            'nombre' => 'Pantalla LCD 15.6"',
            'descripcion' => 'Panel de reemplazo Full HD',
            'cantidad' => 1,
            'precio_unitario' => 150.00,
            'precio_total' => 150.00,
        ]);

        Pieza::create([
            'reparacion_id' => $rep3->id,
            'nombre' => 'Teclado de reemplazo',
            'descripcion' => 'Teclado retroiluminado ES',
            'cantidad' => 1,
            'precio_unitario' => 30.00,
            'precio_total' => 30.00,
        ]);

        NotaReparacion::create([
            'reparacion_id' => $rep3->id,
            'usuario_id' => $tecnicos->first()->id,
            'nota' => 'Pantalla y teclado reemplazados. Equipo funcionando perfectamente.',
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $rep3->id,
            'estado' => 'Finalizado',
            'comentario' => 'Reparación completada satisfactoriamente',
            'usuario_id' => $tecnicos->first()->id,
        ]);

        // Crear más reparaciones para tener datos variados
        for ($i = 4; $i <= 8; $i++) {
            $tecnico = $tecnicos->random();
            $estados = ['Recibido', 'En Diagnóstico', 'En Proceso'];
            $estado = $estados[array_rand($estados)];
            
            Reparacion::create([
                'equipo_id' => $i,
                'tecnico_id' => $tecnico->id,
                'estado' => $estado,
                'fecha_ingreso' => Carbon::now()->subDays(rand(1, 15)),
                'fecha_prometida' => Carbon::now()->addDays(rand(3, 10)),
                'costo_diagnostico' => 50.00,
                'costo_piezas' => rand(50, 300),
                'costo_mano_obra' => rand(60, 150),
                'total_estimado' => rand(160, 500),
            ]);
        }
    }
}
