<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reparacion extends Model
{
    use HasFactory;

    /**
     * Nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'reparaciones';

    protected $fillable = [
        'equipo_id',
        'tecnico_id',
        'diagnostico',
        'piezas_reemplazadas',
        'observaciones',
        'costo_mano_obra',
        'costo_piezas',
        'total',
        'fecha_inicio',
        'fecha_finalizacion',
        'estado',
    ];

    protected $casts = [
        'piezas_reemplazadas' => 'array',
        'costo_mano_obra' => 'decimal:2',
        'costo_piezas' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_inicio' => 'date',
        'fecha_finalizacion' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($reparacion) {
            $reparacion->total = ($reparacion->costo_mano_obra ?? 0) + ($reparacion->costo_piezas ?? 0);
        });
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function garantia()
    {
        return $this->hasOne(Garantia::class);
    }
}
