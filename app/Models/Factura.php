<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'cliente_id',
        'equipo_id',
        'reparacion_id',
        'subtotal',
        'aplicar_impuesto',
        'ncf',
        'impuestos',
        'total',
        'forma_pago',
        'fecha_emision',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'aplicar_impuesto' => 'boolean',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_emision' => 'date',
    ];

    /**
     * Boot del modelo para generar número de factura automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($factura) {
            if (empty($factura->numero_factura)) {
                $factura->numero_factura = 'FAC-' . date('Y') . '-' . str_pad(self::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación con equipo
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Relación con reparación
     */
    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    /**
     * Relación uno a uno con pago
     */
    public function pago()
    {
        return $this->hasOne(Pago::class);
    }
}
