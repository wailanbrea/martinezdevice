<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SistemaConfiguracion extends Model
{
    use HasFactory;

    private const CACHE_KEY = 'config.sistema';

    protected $table = 'sistema_configuracion';

    protected $fillable = [
        'costo_diagnostico',
        'costo_diagnostico_mantenimiento',
        'moneda',
        'simbolo_moneda',
        'porcentaje_comision',
        'ncf_codigo',
        'imprimir_etiqueta_auto',
    ];

    protected $casts = [
        'imprimir_etiqueta_auto' => 'boolean',
        'costo_diagnostico' => 'decimal:2',
        'costo_diagnostico_mantenimiento' => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function obtener()
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::firstOrCreate(['id' => 1], [
                'costo_diagnostico' => 500.00,
                'costo_diagnostico_mantenimiento' => 0.00,
                'moneda' => 'DOP',
                'simbolo_moneda' => '$',
                'porcentaje_comision' => 10.00,
                'imprimir_etiqueta_auto' => true,
            ]);
        });
    }

    public static function obtenerNcfCodigo(): ?string
    {
        $sistema = static::obtener();
        $codigo = trim((string) ($sistema->ncf_codigo ?? ''));
        if ($codigo !== '') {
            return $codigo;
        }
        $factura = FacturaConfiguracion::obtener();
        $codigo = trim((string) ($factura->ncf_codigo ?? ''));
        return $codigo !== '' ? $codigo : null;
    }
}
