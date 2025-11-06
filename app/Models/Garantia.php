<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Garantia extends Model
{
    use HasFactory;

    protected $fillable = [
        'equipo_id',
        'reparacion_id',
        'fecha_inicio',
        'fecha_vencimiento',
        'duracion_dias',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_vencimiento' => 'date',
        'activa' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($garantia) {
            if (empty($garantia->fecha_vencimiento) && $garantia->fecha_inicio) {
                $fechaInicio = is_string($garantia->fecha_inicio) 
                    ? \Carbon\Carbon::parse($garantia->fecha_inicio) 
                    : $garantia->fecha_inicio;
                $garantia->fecha_vencimiento = $fechaInicio->copy()->addDays($garantia->duracion_dias ?? 30);
            }
        });
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function reparacion()
    {
        return $this->belongsTo(Reparacion::class);
    }

    public function estaVigente(): bool
    {
        return $this->activa && $this->fecha_vencimiento >= now();
    }
}
