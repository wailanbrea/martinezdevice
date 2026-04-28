<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarFacturaConfig extends Command
{
    protected $signature = 'factura:verificar-config';
    protected $description = 'Verificar el contenido actual de factura_configuracion en la BD';

    public function handle(): int
    {
        $row = DB::connection()->table('factura_configuracion')->where('id', 1)->first();
        if (!$row) {
            $this->error('No existe registro con id=1 en factura_configuracion');
            return 1;
        }
        $this->info('Contenido de factura_configuracion (id=1):');
        $this->table(
            ['Campo', 'Valor'],
            collect((array) $row)->map(fn ($v, $k) => [$k, $v])->values()->toArray()
        );
        return 0;
    }
}
