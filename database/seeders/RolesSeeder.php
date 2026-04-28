<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\User;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear los roles del sistema
        $roles = [
            [
                'nombre' => 'Administrador',
                'slug' => 'administrador',
                'descripcion' => 'Acceso completo al sistema',
            ],
            [
                'nombre' => 'Técnico',
                'slug' => 'tecnico',
                'descripcion' => 'Gestiona reparaciones y diagnósticos',
            ],
            [
                'nombre' => 'Recepción',
                'slug' => 'recepcion',
                'descripcion' => 'Registra equipos y atiende clientes',
            ],
            [
                'nombre' => 'Contabilidad',
                'slug' => 'contabilidad',
                'descripcion' => 'Gestiona facturación y pagos',
            ],
        ];

        foreach ($roles as $rolData) {
            Rol::firstOrCreate(
                ['slug' => $rolData['slug']],
                $rolData
            );
        }

        // Nota: Los usuarios se crean en CrearUsuariosSeeder
        // Este seeder solo crea los roles del sistema
    }
}
