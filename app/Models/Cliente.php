<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cedula_rnc',
        'telefono',
        'correo',
        'direccion',
    ];

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
}
