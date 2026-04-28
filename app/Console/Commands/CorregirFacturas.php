<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;
use App\Models\Reparacion;
use Illuminate\Support\Facades\DB;

class CorregirFacturas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'facturas:corregir {--reparacion_id= : ID de reparación específica}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Corrige las facturas para que usen el precio_cotizado cuando esté aprobado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reparacionId = $this->option('reparacion_id');
        
        if ($reparacionId) {
            $reparaciones = Reparacion::where('id', $reparacionId)->get();
        } else {
            $reparaciones = Reparacion::whereHas('factura')->get();
        }

        $this->info("Procesando " . $reparaciones->count() . " reparaciones con factura...");

        $corregidas = 0;
        $configFactura = DB::table('factura_configuracion')->first();
        $porcentajeImpuesto = $configFactura->impuesto_porcentaje ?? 18.00;

        foreach ($reparaciones as $reparacion) {
            if (!$reparacion->factura) {
                continue;
            }

            $factura = $reparacion->factura;
            
            // Determinar el precio final correcto
            if ($reparacion->cliente_aprobado === true && $reparacion->precio_cotizado) {
                $precioFinal = $reparacion->precio_cotizado;
            } elseif ($reparacion->precio_cotizado) {
                $precioFinal = $reparacion->precio_cotizado;
            } else {
                $precioFinal = $reparacion->total_estimado ?? 0;
            }

            // Calcular valores correctos
            $subtotal = $precioFinal;
            $impuestos = ($subtotal * $porcentajeImpuesto) / 100;
            $total = $subtotal + $impuestos;

            // Verificar si necesita corrección
            if (abs($factura->subtotal - $subtotal) > 0.01 || abs($factura->total - $total) > 0.01) {
                $this->line("Corrigiendo factura {$factura->numero_factura} para reparación {$reparacion->codigo_reparacion}");
                $this->line("  Subtotal: {$factura->subtotal} → {$subtotal}");
                $this->line("  Impuestos: {$factura->impuestos} → {$impuestos}");
                $this->line("  Total: {$factura->total} → {$total}");

                $factura->update([
                    'subtotal' => $subtotal,
                    'impuestos' => $impuestos,
                    'total' => $total,
                ]);

                $corregidas++;
            }
        }

        $this->info("✓ Se corrigieron {$corregidas} facturas.");
        
        return 0;
    }
}

