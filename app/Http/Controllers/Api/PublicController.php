<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Reparacion;
use App\Models\EstadoReparacion;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PublicController extends Controller
{
    /**
     * Consulta pública del estado de un equipo usando su código único (UUID)
     */
    public function consultarEstado($codigo)
    {
        $equipo = Equipo::where('codigo_unico', $codigo)
            ->with([
                'cliente:id,nombre,telefono',
                'reparaciones' => function($query) {
                    $query->latest()->with([
                        'historialEstados' => function($q) {
                            $q->orderBy('created_at', 'desc');
                        }
                    ]);
                }
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
                'descripcion_cotizacion' => $cotizacionDisponiblePublicamente ? $reparacionActual->descripcion_cotizacion : null,
                'fecha_cotizacion' => $cotizacionDisponiblePublicamente && $reparacionActual->fecha_cotizacion ? $reparacionActual->fecha_cotizacion->format('d/m/Y H:i') : null,
                'cliente_aprobado' => $reparacionActual->cliente_aprobado,
                'fecha_aprobacion' => $reparacionActual->fecha_aprobacion ? $reparacionActual->fecha_aprobacion->format('d/m/Y H:i') : null,
                'puede_aprobar' => $reparacionActual->estado === 'Esperando Aprobación' && $reparacionActual->precio_cotizado > 0 && $reparacionActual->cliente_aprobado === null,
                'historial' => $reparacionActual->historialEstados ? $reparacionActual->historialEstados->map(function($estado) {
                    return [
                        'estado' => $estado->estado,
                        'comentario' => $estado->comentario ?? '',
                        'fecha' => $estado->created_at->format('d/m/Y H:i'),
                    ];
                }) : [],
            ] : null,
        ], 200);
    }

    /**
     * Formulario para que el cliente ingrese su código (entrada a consulta pública)
     * Si viene con ?codigo=XXX redirige a /consulta/XXX
     */
    public function formularioConsulta()
    {
        $codigo = request()->query('codigo');
        if ($codigo !== null && $codigo !== '') {
            return redirect()->route('public.consulta', ['codigo' => trim($codigo)]);
        }
        return view('public.consulta-form');
    }

    /**
     * Vista pública para consultar estado del equipo por código único
     */
    public function vistaConsulta($codigo)
    {
        $equipo = Equipo::where('codigo_unico', $codigo)
            ->with([
                'cliente',
                'fotos',
                'reparaciones' => function($query) {
                    $query->latest()->with([
                        'historialEstados.usuario',
                        'factura'
                    ]);
                }
            ])
            ->first();

        if (!$equipo) {
            return response()->view('public.codigo-no-encontrado', [
                'codigo' => $codigo,
                'urlConsulta' => route('public.consulta.form'),
            ], 404);
        }

        $reparacionActual = $equipo->reparaciones->first();

        // Verificar que el cliente existe
        if (!$equipo->cliente) {
            return response()->view('public.codigo-no-encontrado', [
                'codigo' => $codigo,
                'mensaje' => 'Cliente no encontrado para este equipo.',
                'urlConsulta' => route('public.consulta.form'),
            ], 404);
        }

        // Porcentaje de impuesto por defecto (18%) para mostrar en la vista el total con impuesto
        $configFactura = DB::table('factura_configuracion')->first(['impuesto_porcentaje']);
        $porcentajeImpuesto = $configFactura->impuesto_porcentaje ?? 18.00;

        return view('public.consulta', compact('equipo', 'reparacionActual', 'porcentajeImpuesto'));
    }

    /**
     * Aprobar cotización (API)
     */
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

            // Validar que el estado sea "Esperando Aprobación"
            if ($reparacion->estado !== 'Esperando Aprobación') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reparación no está esperando aprobación. Estado actual: ' . $reparacion->estado,
                ], 400);
            }

            // Validar que tenga precio cotizado
            if (!$reparacion->precio_cotizado || $reparacion->precio_cotizado <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una cotización disponible para aprobar',
                ], 400);
            }

            // Validar que no haya sido aprobada o rechazada previamente
            if ($reparacion->cliente_aprobado !== null) {
                return response()->json([
                    'success' => false,
                    'message' => $reparacion->cliente_aprobado 
                        ? 'Esta cotización ya fue aprobada anteriormente' 
                        : 'Esta cotización ya fue rechazada anteriormente',
                ], 400);
            }

            // Usar transacción para asegurar consistencia
            DB::transaction(function () use ($reparacion) {
                // Actualizar solo aprobación y estado; NUNCA tocar tecnico_id para no quitar la asignación
                $reparacion->cliente_aprobado = true;
                $reparacion->fecha_aprobacion = Carbon::now();
                $reparacion->estado = 'Aprobado';
                $reparacion->saveQuietly(); // save sin eventos para no afectar otros atributos

                // Crear factura automáticamente si no existe
                if (!$reparacion->factura) {
                    // Obtener configuración de factura para calcular impuestos
                    $configFactura = DB::table('factura_configuracion')->first();
                    // Usar la preferencia guardada en la cotización (desmarcar = no impuesto)
                    $aplicarImpuesto = $reparacion->aplicar_impuesto_cotizacion ?? true;
                    $porcentajeImpuesto = $configFactura->impuesto_porcentaje ?? 18.00;
                    
                    // Calcular impuestos
                    $subtotal = $reparacion->precio_cotizado;
                    $impuestos = ($subtotal * $porcentajeImpuesto) / 100;
                    $total = $subtotal + $impuestos;
                    
                    $ncf = null;
                    $ncfCodigo = \App\Models\SistemaConfiguracion::obtenerNcfCodigo();
                    if ($ncfCodigo) {
                        $ultimoNCF = Factura::whereNotNull('ncf')->lockForUpdate()->max('id') ?? 0;
                        $ncf = $ncfCodigo . str_pad($ultimoNCF + 1, 8, '0', STR_PAD_LEFT);
                    }
                    
                    // Generar número de factura con lock para prevenir duplicados
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
                        'forma_pago' => 'efectivo', // Se actualizará cuando se pague
                    ]);
                }

                // Registrar en historial
                EstadoReparacion::create([
                    'reparacion_id' => $reparacion->id,
                    'estado' => 'Aprobado',
                    'comentario' => 'Cotización aprobada por el cliente. El técnico puede proceder con la reparación.',
                    'usuario_id' => null, // Cliente público
                ]);
            });

            // Limpiar caché del dashboard
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

    /**
     * Rechazar cotización (API)
     */
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

            // Validar que el estado sea "Esperando Aprobación"
            if ($reparacion->estado !== 'Esperando Aprobación') {
                return response()->json([
                    'success' => false,
                    'message' => 'Esta reparación no está esperando aprobación. Estado actual: ' . $reparacion->estado,
                ], 400);
            }

            // Validar que tenga precio cotizado
            if (!$reparacion->precio_cotizado || $reparacion->precio_cotizado <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay una cotización disponible para rechazar',
                ], 400);
            }

            // Validar que no haya sido aprobada o rechazada previamente
            if ($reparacion->cliente_aprobado !== null) {
                return response()->json([
                    'success' => false,
                    'message' => $reparacion->cliente_aprobado 
                        ? 'Esta cotización ya fue aprobada anteriormente' 
                        : 'Esta cotización ya fue rechazada anteriormente',
                ], 400);
            }

            $comentario = $request->input('comentario', '');

            // Actualizar reparación
            $reparacion->update([
                'cliente_aprobado' => false,
                'estado' => 'Cancelado',
            ]);

            // Registrar en historial
            EstadoReparacion::create([
                'reparacion_id' => $reparacion->id,
                'estado' => 'Cancelado',
                'comentario' => 'Cotización rechazada por el cliente' . ($comentario ? ': ' . $comentario : ''),
                'usuario_id' => null, // Cliente público
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
