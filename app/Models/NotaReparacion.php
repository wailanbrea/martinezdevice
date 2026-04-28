<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaReparacion extends Model
{
    use HasFactory;

    protected $table = 'notas_reparacion';

    protected $fillable = [
        'reparacion_id',
        'usuario_id',
        'nota',
    ];

    /**
     * Relación con reparación
     */
    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    /**
     * Relación con usuario (quien escribió la nota)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
