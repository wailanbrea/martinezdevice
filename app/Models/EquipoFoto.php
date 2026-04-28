<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EquipoFoto extends Model
{
    use HasFactory;

    protected $table = 'equipo_fotos';

    protected $fillable = [
        'equipo_id',
        'ruta',
        'nombre_original',
        'orden',
    ];

    /**
     * Relación con equipo
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
