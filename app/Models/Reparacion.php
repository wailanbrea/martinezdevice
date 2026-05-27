<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reparacion extends Model
{
    use HasFactory;

    public const ESTADO_LISTO_LEGACY = 'listo';
    public const ESTADO_FINALIZADO = 'Finalizado';
    private const ESTADO_ALIASES = [
        'finalizado' => ['Finalizado', 'FINALIZADO', 'finalizado', 'listo'],
        'aprobado' => ['Aprobado', 'APROBADO', 'aprobado'],
        'recibido' => ['Recibido', 'RECIBIDO', 'recibido'],
        'en diagnóstico' => ['En Diagnóstico', 'EN DIAGNÓSTICO', 'En Diagnostico', 'EN DIAGNOSTICO', 'en diagnóstico', 'en diagnostico'],
        'pendiente revisión admin' => ['Pendiente Revisión Admin', 'PENDIENTE REVISIÓN ADMIN', 'Pendiente Revision Admin', 'PENDIENTE REVISION ADMIN'],
        'esperando aprobación' => ['Esperando Aprobación', 'ESPERANDO APROBACIÓN', 'Esperando Aprobacion', 'ESPERANDO APROBACION'],
        'esperando pieza' => ['Esperando Pieza', 'ESPERANDO PIEZA', 'esperando pieza'],
        'en proceso' => ['En Proceso', 'EN PROCESO', 'en proceso'],
        'sin reparación' => ['Sin Reparación', 'SIN REPARACIÓN', 'Sin Reparacion', 'SIN REPARACION', 'sin reparación', 'sin reparacion'],
        'entregado' => ['Entregado', 'ENTREGADO', 'entregado'],
        'cancelado' => ['Cancelado', 'CANCELADO', 'cancelado'],
    ];

    protected $table = 'reparaciones';

    protected $fillable = [
        'codigo_reparacion',
        'equipo_id',
        'tecnico_id',
        'tecnico_completo_id',
        'recepcionista_id',
        'estado',
        'tipo_servicio',
        'es_garantia',
        'periodo_garantia_dias',
        'fecha_vencimiento_garantia',
        'reparacion_original_id',
        'fecha_ingreso',
        'fecha_prometida',
        'fecha_finalizacion',
        'costo_diagnostico',
        'costo_piezas',
        'costo_mano_obra',
        'total_estimado',
        'precio_cotizado',
        'descripcion_cotizacion',
        'aplicar_impuesto_cotizacion',
        'cotizacion_revisada_por',
        'cotizacion_revisada_at',
        'cliente_aprobado',
        'fecha_cotizacion',
        'fecha_aprobacion',
        'porcentaje_comision',
        'monto_comision',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_prometida' => 'date',
        'fecha_finalizacion' => 'date',
        'fecha_vencimiento_garantia' => 'date',
        'fecha_cotizacion' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'cotizacion_revisada_at' => 'datetime',
        'costo_diagnostico' => 'decimal:2',
        'costo_piezas' => 'decimal:2',
        'costo_mano_obra' => 'decimal:2',
        'total_estimado' => 'decimal:2',
        'precio_cotizado' => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
        'monto_comision' => 'decimal:2',
        'cliente_aprobado' => 'boolean',
        'aplicar_impuesto_cotizacion' => 'boolean',
        'es_garantia' => 'boolean',
        'periodo_garantia_dias' => 'integer',
    ];

    /**
     * Boot del modelo para generar código de reparación automáticamente
     * NOTA: La generación del código debe hacerse en el controlador con lockForUpdate
     * para evitar condiciones de carrera. Este método es un fallback.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($reparacion) {
            if (empty($reparacion->codigo_reparacion)) {
                // Fallback: generar código si no se proporcionó uno
                // El controlador debe generar el código con lockForUpdate para thread-safety
                $ultimoId = static::lockForUpdate()->max('id') ?? 0;
                $reparacion->codigo_reparacion = 'REP-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relación con equipo
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Relación con técnico (usuario)
     */
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_id');
    }

    /**
     * Relación con recepcionista (usuario que recibió el equipo)
     */
    public function recepcionista()
    {
        return $this->belongsTo(User::class, 'recepcionista_id');
    }

    /**
     * Relación con técnico que completó el trabajo (para comisiones)
     */
    public function tecnicoCompleto()
    {
        return $this->belongsTo(User::class, 'tecnico_completo_id');
    }

    /**
     * Relación con el administrador que revisó la cotización.
     */
    public function cotizacionRevisadaPor()
    {
        return $this->belongsTo(User::class, 'cotizacion_revisada_por');
    }

    /**
     * Relación uno a muchos con piezas
     */
    public function piezas()
    {
        return $this->hasMany(Pieza::class);
    }

    /**
     * Relación uno a muchos con notas
     */
    public function notas()
    {
        return $this->hasMany(NotaReparacion::class);
    }

    /**
     * Relación uno a muchos con historial de estados
     */
    public function historialEstados()
    {
        return $this->hasMany(EstadoReparacion::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relación uno a uno con factura
     */
    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    /**
     * Relación uno a muchos con pagos
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    /**
     * Relación con reparación original (si esta es una garantía)
     */
    public function reparacionOriginal()
    {
        return $this->belongsTo(Reparacion::class, 'reparacion_original_id');
    }

    /**
     * Relación con reparaciones en garantía de esta reparación
     */
    public function reparacionesGarantia()
    {
        return $this->hasMany(Reparacion::class, 'reparacion_original_id');
    }

    public function getEstadoAttribute($value)
    {
        return $value === self::ESTADO_LISTO_LEGACY
            ? self::ESTADO_FINALIZADO
            : $value;
    }

    public function setEstadoAttribute($value): void
    {
        $this->attributes['estado'] = $value === self::ESTADO_LISTO_LEGACY
            ? self::ESTADO_FINALIZADO
            : $value;
    }

    public function scopeWhereEstadoNormalizado($query, ?string $estado)
    {
        if ($estado === null || $estado === '') {
            return $query;
        }

        $variantes = self::variantesEstado($estado);

        return $query->where(function ($subQuery) use ($variantes) {
            foreach (array_unique($variantes) as $variante) {
                $subQuery->orWhereRaw('LOWER(estado) = LOWER(?)', [$variante]);
            }
        });
    }

    public static function variantesEstado(string $estado): array
    {
        $estadoNormalizado = mb_strtolower(trim($estado));
        $variantes = self::ESTADO_ALIASES[$estadoNormalizado] ?? [$estado];

        if (in_array(self::ESTADO_LISTO_LEGACY, $variantes, true) && !in_array(self::ESTADO_FINALIZADO, $variantes, true)) {
            $variantes[] = self::ESTADO_FINALIZADO;
        }

        return array_values(array_unique($variantes));
    }
}
