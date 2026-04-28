<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClientesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Ana García Martínez',
                'cedula_rnc' => '402-1234567-8',
                'telefono' => '+1 (809) 555-0123',
                'email' => 'ana.garcia@email.com',
                'direccion' => 'Calle Principal #45, Santo Domingo',
            ],
            [
                'nombre' => 'Juan Pérez López',
                'cedula_rnc' => '001-2345678-9',
                'telefono' => '+1 (829) 555-0456',
                'email' => 'juan.perez@email.com',
                'direccion' => 'Av. 27 de Febrero #123, Santiago',
            ],
            [
                'nombre' => 'María Rodríguez',
                'cedula_rnc' => '402-3456789-0',
                'telefono' => '+1 (849) 555-0789',
                'email' => 'maria.rodriguez@email.com',
                'direccion' => 'Calle Duarte #67, La Vega',
            ],
            [
                'nombre' => 'Luis Martínez Sánchez',
                'cedula_rnc' => '001-4567890-1',
                'telefono' => '+1 (809) 555-1234',
                'email' => 'luis.martinez@email.com',
                'direccion' => 'Av. Independencia #89, Santo Domingo',
            ],
            [
                'nombre' => 'Elena Fernández',
                'cedula_rnc' => '402-5678901-2',
                'telefono' => '+1 (829) 555-5678',
                'email' => 'elena.fernandez@email.com',
                'direccion' => 'Calle Mella #34, San Pedro de Macorís',
            ],
            [
                'nombre' => 'TechStore RD',
                'cedula_rnc' => '1-31-12345-6',
                'telefono' => '+1 (809) 555-9012',
                'email' => 'contacto@techstore.com',
                'direccion' => 'Plaza Comercial Las Américas, Local 45',
            ],
        ];

        foreach ($clientes as $clienteData) {
            Cliente::create($clienteData);
        }
    }
}
