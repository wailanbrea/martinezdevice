<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'codigo_unico',
        'tipo',
        'tipo_personalizado',
        'marca',
        'modelo',
        'numero_serie',
        'descripcion_problema',
        'foto',
        'codigo_qr',
        'estado',
    ];

    protected $casts = [
        'codigo_unico' => 'string',
    ];

    /**
     * Boot del modelo para generar UUID automáticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipo) {
            if (empty($equipo->codigo_unico)) {
                $equipo->codigo_unico = (string) Str::uuid();
            }
        });
    }

    /**
     * Relación con cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Relación uno a muchos con reparaciones
     */
    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class);
    }

    /**
     * Relación uno a muchos con estados de reparación
     */
    public function estadosReparacion()
    {
        return $this->hasManyThrough(EstadoReparacion::class, Reparacion::class);
    }

    /**
     * Relación con facturas
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    /**
     * Relación uno a muchos con fotos
     */
    public function fotos()
    {
        return $this->hasMany(EquipoFoto::class)->orderBy('orden');
    }
}
