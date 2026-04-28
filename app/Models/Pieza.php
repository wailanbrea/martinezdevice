<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pieza extends Model
{
    use HasFactory;

    protected $fillable = [
        'reparacion_id',
        'nombre',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'precio_total',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'precio_total' => 'decimal:2',
    ];

    /**
     * Relación con reparación
     */
    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }
}
