<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'tipo',
        'marca',
        'modelo',
        'numero_serie',
        'descripcion_falla',
        'estado',
        'foto',
        'codigo_unico',
        'codigo_qr',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($equipo) {
            if (empty($equipo->codigo_unico)) {
                $equipo->codigo_unico = 'EQ-' . strtoupper(Str::random(10));
            }
        });

        static::created(function ($equipo) {
            HistorialEstado::create([
                'equipo_id' => $equipo->id,
                'estado_anterior' => null,
                'estado_nuevo' => $equipo->estado,
                'usuario_id' => auth()->id(),
            ]);
        });

        static::updated(function ($equipo) {
            if ($equipo->isDirty('estado')) {
                HistorialEstado::create([
                    'equipo_id' => $equipo->id,
                    'estado_anterior' => $equipo->getOriginal('estado'),
                    'estado_nuevo' => $equipo->estado,
                    'usuario_id' => auth()->id(),
                ]);
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class);
    }

    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class);
    }

    public function garantias()
    {
        return $this->hasMany(Garantia::class);
    }

    public function generarQR(): void
    {
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $url = url("/api/public/status/{$this->codigo_unico}");
            // Generar QR como SVG string
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($url);
            $this->codigo_qr = $qrCode;
            $this->save();
        }
    }

    /**
     * Obtener la URL de la foto del equipo
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return \Illuminate\Support\Facades\Storage::url($this->foto);
        }
        return null;
    }
}
