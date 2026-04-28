<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pago extends Model
{
    use HasFactory;

    protected $fillable = [
        'factura_id',
        'reparacion_id',
        'usuario_id',
        'monto',
        'tipo_pago',
        'fecha_pago',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    /**
     * Relación con factura
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    /**
     * Relación con reparación
     */
    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    /**
     * Relación con usuario (quien registró el pago)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
