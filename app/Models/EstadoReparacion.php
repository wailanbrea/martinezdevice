<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EstadoReparacion extends Model
{
    use HasFactory;

    protected $table = 'estados_reparacion';

    protected $fillable = [
        'reparacion_id',
        'estado',
        'comentario',
        'usuario_id',
    ];

    /**
     * Relación con reparación
     */
    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    /**
     * Relación con usuario (quien cambió el estado)
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
