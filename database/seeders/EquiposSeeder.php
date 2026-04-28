<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Equipo;

class EquiposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipos = [
            [
                'cliente_id' => 1,
                'tipo' => 'PC de Escritorio',
                'marca' => 'Custom Build',
                'modelo' => 'PC Gamer (i7-12700K, RTX 3080)',
                'numero_serie' => 'PC-2024-001',
                'descripcion_problema' => 'El equipo no enciende, se escuchan pitidos largos al intentar arrancar. Posible falla en RAM o motherboard.',
                'estado' => 'reparacion',
            ],
            [
                'cliente_id' => 2,
                'tipo' => 'Tarjeta Gráfica (GPU)',
                'marca' => 'NVIDIA',
                'modelo' => 'GeForce RTX 3080',
                'numero_serie' => 'SN-54321-ABC',
                'descripcion_problema' => 'No da video, los ventiladores giran al máximo. Posible falla en chip VRAM.',
                'estado' => 'diagnostico',
            ],
            [
                'cliente_id' => 3,
                'tipo' => 'Laptop',
                'marca' => 'HP',
                'modelo' => 'Pavilion Gaming 15',
                'numero_serie' => 'HP-PAV-789456',
                'descripcion_problema' => 'Pantalla con líneas verticales, teclado no responde bien.',
                'estado' => 'listo',
            ],
            [
                'cliente_id' => 4,
                'tipo' => 'PC de Escritorio',
                'marca' => 'Dell',
                'modelo' => 'OptiPlex 7080',
                'numero_serie' => 'DELL-OPT-456123',
                'descripcion_problema' => 'Windows no inicia, disco duro hace ruidos.',
                'estado' => 'recibido',
            ],
            [
                'cliente_id' => 5,
                'tipo' => 'Laptop',
                'marca' => 'Apple',
                'modelo' => 'MacBook Pro 13" M1',
                'numero_serie' => 'APPLE-MBP-123789',
                'descripcion_problema' => 'No carga la batería, se apaga repentinamente.',
                'estado' => 'diagnostico',
            ],
            [
                'cliente_id' => 1,
                'tipo' => 'Tarjeta Gráfica (GPU)',
                'marca' => 'AMD',
                'modelo' => 'Radeon RX 6800 XT',
                'numero_serie' => 'AMD-RX6800-987654',
                'descripcion_problema' => 'Artefactos en pantalla durante juegos, temperaturas elevadas.',
                'estado' => 'recibido',
            ],
            [
                'cliente_id' => 3,
                'tipo' => 'Consola de Videojuegos',
                'marca' => 'Sony',
                'modelo' => 'PlayStation 5',
                'numero_serie' => 'PS5-2024-456789',
                'descripcion_problema' => 'No lee discos, emite sonido de clic al intentar leer.',
                'estado' => 'reparacion',
            ],
            [
                'cliente_id' => 6,
                'tipo' => 'PC de Escritorio',
                'marca' => 'MSI',
                'modelo' => 'MAG Infinite S3',
                'numero_serie' => 'MSI-MAG-741852',
                'descripcion_problema' => 'Reinicios aleatorios durante uso, pantalla azul frecuente.',
                'estado' => 'diagnostico',
            ],
        ];

        foreach ($equipos as $equipoData) {
            Equipo::create($equipoData);
        }
    }
}
