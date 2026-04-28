<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FacturaConfiguracion;
use App\Models\SistemaConfiguracion;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Asegurar configuraciones base para instalaciones nuevas o migraciones viejas.
        FacturaConfiguracion::obtener();
        SistemaConfiguracion::obtener();

        // Solo se usan los usuarios de Martinez Service (no admin@argon.com)
        $this->call([
            RolesSeeder::class,
            CrearUsuariosSeeder::class,
            LimpiezaCompletaSeeder::class,
        ]);
    }
}
