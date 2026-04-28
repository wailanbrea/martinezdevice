<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Rol;

class CrearUsuariosSeeder extends Seeder
{
    /**
     * Crear usuarios del sistema
     */
    public function run(): void
    {
        // Obtener roles (asumiendo que ya existen)
        $adminRole = Rol::where('slug', 'administrador')->first();
        $tecnicoRole = Rol::where('slug', 'tecnico')->first();

        if (!$adminRole || !$tecnicoRole) {
            $this->command->warn('⚠️  Los roles no existen. Ejecuta primero: php artisan db:seed --class=RolesSeeder');
            return;
        }

        // 1. Usuario Martinez - Administrador (acceso completo)
        $martinez = User::where('email', 'martinez@martinezservice.com')
            ->orWhere('username', 'martinez')
            ->first();
        if (!$martinez) {
            try {
                $martinez = User::create([
                    'username' => 'martinez',
                    'firstname' => 'Martinez',
                    'lastname' => '',
                    'email' => 'martinez@martinezservice.com',
                    'password' => 'martinez123',
                ]);
                $this->command->info('✅ Usuario Martinez (Administrador) creado');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  No se pudo crear Martinez: ' . $e->getMessage());
                $martinez = User::where('username', 'martinez')->first();
            }
        } else {
            $martinez->password = 'martinez123';
            $martinez->save();
            $this->command->info('✅ Contraseña de Martinez actualizada');
        }
        // Asegurar que tenga los roles de administrador y técnico (un usuario puede tener 1 o más roles)
        if ($martinez && $adminRole && $tecnicoRole) {
            $martinez->roles()->sync([$adminRole->id, $tecnicoRole->id]);
            $this->command->info('✅ Roles administrador y técnico asignados a Martinez');
        } elseif ($martinez && $adminRole) {
            $martinez->roles()->sync([$adminRole->id]);
            $this->command->info('✅ Rol administrador asignado a Martinez');
        }

        // 2. Usuario Carlos - Técnico
        $carlos = User::where('email', 'carlos@martinezservice.com')
            ->orWhere('username', 'carlos')
            ->first();
        if (!$carlos) {
            try {
                $carlos = User::create([
                    'username' => 'carlos',
                    'firstname' => 'Carlos',
                    'lastname' => '',
                    'email' => 'carlos@martinezservice.com',
                    'password' => 'carlos123',
                ]);
                $this->command->info('✅ Usuario Carlos (Técnico) creado');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  No se pudo crear Carlos: ' . $e->getMessage());
                $carlos = User::where('username', 'carlos')->first();
            }
        } else {
            $carlos->password = 'carlos123';
            $carlos->save();
            $this->command->info('✅ Contraseña de Carlos actualizada');
        }
        // Asegurar que tenga el rol de técnico
        if ($carlos && $tecnicoRole) {
            // Remover otros roles y asignar solo técnico
            $carlos->roles()->sync([$tecnicoRole->id]);
            $this->command->info('✅ Rol técnico asignado a Carlos');
        }

        // 3. Usuario Ismael - Técnico
        $ismael = User::where('email', 'ismael@martinezservice.com')
            ->orWhere('username', 'ismael')
            ->first();
        if (!$ismael) {
            try {
                $ismael = User::create([
                    'username' => 'ismael',
                    'firstname' => 'Ismael',
                    'lastname' => '',
                    'email' => 'ismael@martinezservice.com',
                    'password' => 'ismael123',
                ]);
                $this->command->info('✅ Usuario Ismael (Técnico) creado');
            } catch (\Exception $e) {
                $this->command->warn('⚠️  No se pudo crear Ismael: ' . $e->getMessage());
                $ismael = User::where('username', 'ismael')->first();
            }
        } else {
            $ismael->password = 'ismael123';
            $ismael->save();
            $this->command->info('✅ Contraseña de Ismael actualizada');
        }
        // Asegurar que tenga el rol de técnico
        if ($ismael && $tecnicoRole) {
            // Remover otros roles y asignar solo técnico
            $ismael->roles()->sync([$tecnicoRole->id]);
            $this->command->info('✅ Rol técnico asignado a Ismael');
        }

        $this->command->info('');
        $this->command->info('📋 Usuarios disponibles:');
        $this->command->info('   👤 Administrador: martinez@martinezservice.com (password: martinez123)');
        $this->command->info('   🔧 Técnico 1: carlos@martinezservice.com (password: carlos123)');
        $this->command->info('   🔧 Técnico 2: ismael@martinezservice.com (password: ismael123)');

        Cache::forget('users.tecnicos');
    }
}

