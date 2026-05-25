<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\EstadoReparacion;
use App\Models\Factura;
use App\Models\FacturaConfiguracion;
use App\Models\Reparacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Consulta publica del estado de un equipo usando su codigo unico (UUID)
     */
    public function consultarEstado($codigo)
    {
        $equipo = Equipo::where('codigo_unico', $codigo)
            ->with([
                'cliente:id,nombre,telefono',
                'reparaciones' => function ($query) {
                    $query->latest()->with([
                        'factura',
                        'historialEstados' => function ($q) {
                            $q->orderBy('created_at', 'desc');
                        },
                    ]);
                },
            ])
            ->first();

        if (!$equipo) {
            return response()->json([
                'success' => false,
                'message' => 'Equipo no encontrado',
            ], 404);
        }

        $reparacionActual = $equipo->reparaciones->first();
        $cotizacionDisponiblePublicamente = $reparacionActual
            && $reparacionActual->estado !== 'Pendiente Revisión Admin';

        $configFactura = FacturaConfiguracion::obtener();
        $impuestosActivos = $configFactura->impuestosHabilitados();
        $usaImpuestos = $reparacionActual
            ? ($impuestosActivos && ($reparacionActual->factura?->aplicar_impuesto ?? $reparacionActual->aplicar_impuesto_cotizacion ?? false))
            : false;
        $subtotal = (float) ($reparacionActual?->factura?->subtotal
            ?? ($reparacionActual?->precio_cotizado ?: $reparacionActual?->total_estimado ?: 0));
        $impuestos = $usaImpuestos
            ? (float) ($reparacionActual?->factura?->impuestos ?? round($subtotal * ($configFactura->porcentajeImpuestoActivo() / 100), 2))
            : 0;
        $total = $reparacionActual?->factura
            ? (float) $reparacionActual->factura->total
            : ($subtotal + $impuestos);

        return response()->json([
            'success' => true,
            'equipo' => [
                'tipo' => $equipo->tipo,
                'tipo_personalizado' => $equipo->tipo_personalizado,
                'marca' => $equipo->marca,
                'modelo' => $equipo->modelo,
                'numero_serie' => $equipo->numero_serie,
                'estado' => $equipo->estado,
            ],
            'cliente' => $equipo->cliente ? [
                'nombre' => $equipo->cliente->nombre,
                'telefono' => $equipo->cliente->telefono,
            ] : null,
            'reparacion' => $reparacionActual ? [
                'codigo' => $reparacionActual->codigo_reparacion,
                'estado' => $reparacionActual->estado,
                'tipo_servicio' => $reparacionActual->tipo_servicio,
                'fecha_ingreso' => $reparacionActual->fecha_ingreso ? $reparacionActual->fecha_ingreso->format('d/m/Y') : null,
                'fecha_prometida' => $reparacionActual->fecha_prometida?->format('d/m/Y'),
                'precio_cotizado' => $cotizacionDisponiblePublicamente && $reparacionActual->precio_cotizado ? number_format($reparacionActual->precio_cotizado, 2) : null,
                'subtotal' => $cotizacionDisponiblePublicamente && $subtotal > 0 ? number_format($subtotal, 2) : null,
                'impuestos' => $cotizacionDisponiblePublicamente && $usaImpuestos ? number_format($impuestos, 2) : null,
                'total' => $cotizacionDisponiblePublicamente && $total > 0 ? number_format($total, 2) : null,
                'usa_impuestos' => $usaImpuestos,
                'descripcion_cotizacion' => $cotizacionDisponiblePublicamente ? $reparacionActual->descripcion_cotizacion : null,
                'fecha_cotizacion' => $cotizacionDisponiblePublicamente && $reparacionActual->fecha_cotizacion ? $reparacionActual->fecha_cotizacion->format('d/m/Y H:i') : null,
                'cliente_aprobado' => $reparacionActual->cliente_aprobado,
                'fecha_aprobacion' => $reparacionActual->fecha_aprobacion ? $reparacionActual->fecha_aprobacion->format('d/m/Y H:i') : null,
                'puede_aprobar' => $reparacionActual->estado === 'Esperando Aprobación' && $reparacionActual->precio_cotizado > 0 && $reparacionActual->cliente_aprobado === null,
                'historial' => $reparacionActual->historialEstados ? $reparacionActual->historialEstados->map(function ($estado) {
                    return [
                        'estado' => $estado->estado,
                        'comentario' => $estado->comentario ?? '',
                        'fecha' => $estado->created_at->format('d/m/Y H:i'),
                    ];
                }) : [],
            ] : null,
        ], 200);
    }

    public function formularioConsulta()
    {
        $codigo = request()->query('codigo');
        if ($codigo !== null && $codigo !== '') {
            return redirect()->route('public.consulta', ['codigo' => trim($codigo)]);
        }

        return view('public.consulta-form');
    }

    public function vistaConsulta($codigo)
    {
        $equipo = Equipo::where('codigo_unico', $codigo)
            ->with([
                'cliente',
                'fotos',
                'reparaciones' => function ($query) {
                    $query->latest()->with([
                        'historialEstados.usuario',
                        'factura',
                    ]);
                },
            ])
            ->first();

        if (!$equipo) {
            return response()->view('public.codigo-no-encontrado', [
                'codigo' => $codigo,
                'urlConsulta' => route('public.consulta.form'),
            ], 404);
        }

        $reparacionActual = $equipo->reparaciones->first();

        if (!$equipo->cliente) {
            return response()->view('public.codigo-no-encontrado', [
                'codigo' => $codigo,
                'mensaje' => 'Cliente no encontrado para este equipo.',
                'urlConsulta' => route('public.consulta.form'),
            ], 404);
        }

        $configFactura = FacturaConfiguracion::obtener();
        $porcentajeImpuesto = $configFactura->porcentajeImpuestoActivo();
        $impuestosActivos = $configFactura->impuestosHabilitados();

        return view('public.consulta', compact('equipo', 'reparacionActual', 'porcentajeImpuesto', 'impuestosActivos'));
    }

    public function aprobarCotizacion(Request $request, $codigo)
    {
        try {
            $equipo = Equipo::where('codigo_unico', $codigo)->first();

            if (!$equipo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipo no encontrado',
                ], 404);
            }

            $reparacion = Reparacion::where('equipo_id', $equipo->id)
                ->latest()
                ->first();

            if (!$reparacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una reparación para este equipo',
                ], 404);
            }

            if ($reparacion->estado !== 'Esperando Aprobación') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reparación no está esperando aprobación. Estado actual: ' . $reparacion->estado,
                ], 400);
            }

            if (!$reparacion->precio_cotizado || $reparacion->precio_cotizado <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una cotización disponible para aprobar',
                ], 400);
            }

            if ($reparacion->cliente_aprobado !== null) {
                return response()->json([
                    'success' => false,
                    'message' => $reparacion->cliente_aprobado
                        ? 'Esta cotización ya fue aprobada anteriormente'
                        : 'Esta cotización ya fue rechazada anteriormente',
                ], 400);
            }

            DB::transaction(function () use ($reparacion) {
                $reparacion->cliente_aprobado = true;
                $reparacion->fecha_aprobacion = Carbon::now();
                $reparacion->estado = 'Aprobado';
                $reparacion->saveQuietly();

                if (!$reparacion->factura) {
                    $configFactura = FacturaConfiguracion::obtener();
                    $aplicarImpuesto = $configFactura->debeAplicarImpuesto($reparacion->aplicar_impuesto_cotizacion ?? false);
                    $porcentajeImpuesto = $configFactura->porcentajeImpuestoActivo();

                    $subtotal = $reparacion->precio_cotizado;
                    $impuestos = $aplicarImpuesto ? (($subtotal * $porcentajeImpuesto) / 100) : 0;
                    $total = $subtotal + $impuestos;

                    $ncf = null;
                    $ncfCodigo = \App\Models\SistemaConfiguracion::obtenerNcfCodigo();
                    if ($aplicarImpuesto && $ncfCodigo) {
                        $ultimoNCF = Factura::whereNotNull('ncf')->lockForUpdate()->max('id') ?? 0;
                        $ncf = $ncfCodigo . str_pad($ultimoNCF + 1, 8, '0', STR_PAD_LEFT);
                    }

                    $ultimoId = Factura::lockForUpdate()->max('id') ?? 0;
                    $numeroFactura = 'FAC-' . date('Y') . '-' . str_pad($ultimoId + 1, 6, '0', STR_PAD_LEFT);

                    Factura::create([
                        'reparacion_id' => $reparacion->id,
                        'equipo_id' => $reparacion->equipo_id,
                        'cliente_id' => $reparacion->equipo->cliente_id,
                        'numero_factura' => $numeroFactura,
                        'fecha_emision' => Carbon::now(),
                        'subtotal' => $subtotal,
                        'aplicar_impuesto' => $aplicarImpuesto,
                        'ncf' => $ncf,
                        'impuestos' => $impuestos,
                        'total' => $total,
                        'forma_pago' => 'efectivo',
                    ]);
                }

                EstadoReparacion::create([
                    'reparacion_id' => $reparacion->id,
                    'estado' => 'Aprobado',
                    'comentario' => 'Cotización aprobada por el cliente. El técnico puede proceder con la reparación.',
                    'usuario_id' => null,
                ]);
            });

            Cache::forget('dashboard.siguiente_mantenimiento');
            Cache::forget('dashboard.siguiente_reparacion');
            Cache::forget('dashboard.siguiente_gpu');
            Cache::forget('dashboard.total_mantenimientos');
            Cache::forget('dashboard.total_reparaciones');
            Cache::forget('dashboard.total_gpus');
            Cache::forget('dashboard.stats');

            return response()->json([
                'success' => true,
                'message' => 'Cotización aprobada exitosamente. El técnico procederá con la reparación.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la aprobación: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function rechazarCotizacion(Request $request, $codigo)
    {
        try {
            $equipo = Equipo::where('codigo_unico', $codigo)->first();

            if (!$equipo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipo no encontrado',
                ], 404);
            }

            $reparacion = Reparacion::where('equipo_id', $equipo->id)
                ->latest()
                ->first();

            if (!$reparacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró una reparación para este equipo',
                ], 404);
            }

            if ($reparacion->estado !== 'Esperando Aprobación') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reparación no está esperando aprobación. Estado actual: ' . $reparacion->estado,
                ], 400);
            }

            if (!$reparacion->precio_cotizado || $reparacion->precio_cotizado <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una cotización disponible para rechazar',
                ], 400);
            }

            if ($reparacion->cliente_aprobado !== null) {
                return response()->json([
                    'success' => false,
                    'message' => $reparacion->cliente_aprobado
                        ? 'Esta cotización ya fue aprobada anteriormente'
                        : 'Esta cotización ya fue rechazada anteriormente',
                ], 400);
            }

            $comentario = $request->input('comentario', '');

            $reparacion->update([
                'cliente_aprobado' => false,
                'estado' => 'Cancelado',
            ]);

            EstadoReparacion::create([
                'reparacion_id' => $reparacion->id,
                'estado' => 'Cancelado',
                'comentario' => 'Cotización rechazada por el cliente' . ($comentario ? ': ' . $comentario : ''),
                'usuario_id' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cotización rechazada. La reparación ha sido cancelada.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el rechazo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
