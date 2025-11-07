<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ejecutar RolSeeder primero
        $this->call(RolSeeder::class);

        // Crear usuario administrador
        // Insertar directamente para evitar problemas con SensitiveParameterValue
        $adminExists = DB::table('users')->where('email', 'admin@martinezservice.com')->exists();
        if (!$adminExists) {
            $passwordHash = password_hash('password', PASSWORD_BCRYPT);
            $adminId = DB::table('users')->insertGetId([
                'name' => 'Administrador',
                'email' => 'admin@martinezservice.com',
                'password' => $passwordHash,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $admin = User::find($adminId);
        } else {
            $admin = User::where('email', 'admin@martinezservice.com')->first();
        }

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
