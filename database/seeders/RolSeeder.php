<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Administrador',
                'slug' => 'administrador',
                'descripcion' => 'Acceso completo al sistema',
            ],
            [
                'nombre' => 'Técnico',
                'slug' => 'tecnico',
                'descripcion' => 'Gestiona reparaciones y equipos',
            ],
            [
                'nombre' => 'Recepción',
                'slug' => 'recepcion',
                'descripcion' => 'Recibe equipos y clientes',
            ],
            [
                'nombre' => 'Contabilidad',
                'slug' => 'contabilidad',
                'descripcion' => 'Acceso a facturación y reportes',
            ],
        ];

        foreach ($roles as $rol) {
            Rol::firstOrCreate(['slug' => $rol['slug']], $rol);
        }
    }
}
