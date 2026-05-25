<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FacturaConfiguracion extends Model
{
    use HasFactory;

    private const CACHE_KEY = 'config.factura';

    protected $table = 'factura_configuracion';

    protected $fillable = [
        'empresa_nombre',
        'empresa_cedula_rnc',
        'empresa_direccion',
        'empresa_telefono',
        'empresa_email',
        'empresa_website',
        'logo_path',
        'encabezado_factura',
        'pie_factura',
        'terminos_condiciones',
        'impuesto_porcentaje',
        'impuestos_activos',
        'ncf_codigo',
        'moneda',
        'simbolo_moneda',
        'mostrar_logo',
        'mostrar_terminos',
        'formato_numero_factura',
    ];

    protected $casts = [
        'impuesto_porcentaje' => 'decimal:2',
        'impuestos_activos' => 'boolean',
        'mostrar_logo' => 'boolean',
        'mostrar_terminos' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public function impuestosHabilitados(): bool
    {
        return (bool) ($this->impuestos_activos ?? true);
    }

    public function porcentajeImpuestoActivo(): float
    {
        if (!$this->impuestosHabilitados()) {
            return 0.0;
        }

        return (float) ($this->impuesto_porcentaje ?? 0);
    }

    public function debeAplicarImpuesto(?bool $preferencia = true): bool
    {
        return $this->impuestosHabilitados() && (bool) $preferencia;
    }

    /**
     * Obtener la configuración única (singleton)
     * Siempre usa el registro con id=1 para evitar lecturas inconsistentes
     */
    public static function obtener()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $config = static::find(1);
            if ($config) {
                return $config;
            }

            return static::firstOrCreate(['id' => 1]);
        });
    }
}
