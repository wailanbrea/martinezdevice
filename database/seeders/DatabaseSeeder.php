<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ejecutar RolSeeder primero
        $this->call(RolSeeder::class);

        // Crear usuario administrador
        $admin = User::firstOrCreate(
            ['email' => 'admin@martinezservice.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
            ]
        );

        // Asegurar que siempre tenga el rol de administrador
        $rolAdmin = Rol::where('slug', 'administrador')->first();
        if ($rolAdmin) {
            // Usar syncWithoutDetaching para no eliminar otros roles si los tiene
            if (!$admin->roles()->where('roles.id', $rolAdmin->id)->exists()) {
                $admin->roles()->attach($rolAdmin->id);
            }
        }

        $this->command->info('Usuario administrador creado/actualizado correctamente.');
        $this->command->info('Email: admin@martinezservice.com');
        $this->command->info('Password: password');

        // Crear datos de prueba
        $this->call(DatosPruebaSeeder::class);
    }
}
