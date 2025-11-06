<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Rol;

class AssignAdminRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar rol de administrador a un usuario';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            return 1;
        }
        
        $rolAdmin = Rol::where('slug', 'administrador')->first();
        
        if (!$rolAdmin) {
            $this->error("Rol de administrador no encontrado. Ejecuta primero: php artisan db:seed --class=RolSeeder");
            return 1;
        }
        
        if ($user->tieneRol('administrador')) {
            $this->info("El usuario {$email} ya tiene el rol de administrador.");
            return 0;
        }
        
        $user->roles()->attach($rolAdmin->id);
        
        $this->info("✓ Rol de administrador asignado correctamente a {$email}.");
        return 0;
    }
}
