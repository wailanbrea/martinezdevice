<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialEstado extends Model
{
    use HasFactory;

    protected $table = 'historial_estados';

    protected $fillable = [
        'equipo_id',
        'estado_anterior',
        'estado_nuevo',
        'observaciones',
        'usuario_id',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
