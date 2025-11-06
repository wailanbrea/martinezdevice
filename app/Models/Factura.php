<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_factura',
        'cliente_id',
        'equipo_id',
        'reparacion_id',
        'subtotal',
        'impuestos',
        'total',
        'forma_pago',
        'fecha_emision',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_emision' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($factura) {
            if (empty($factura->numero_factura)) {
                $ultima = self::latest('id')->first();
                $numero = $ultima ? (int) substr($ultima->numero_factura, 3) + 1 : 1;
                $factura->numero_factura = 'FAC-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }
}
