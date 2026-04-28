<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cedula_rnc',
        'telefono',
        'email',
        'direccion',
    ];

    /**
     * Relación uno a muchos con equipos
     */
    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    /**
     * Relación uno a muchos con facturas
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
