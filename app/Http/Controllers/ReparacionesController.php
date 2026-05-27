<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reparacion;
use App\Models\Equipo;
use App\Models\EquipoFoto;
use App\Models\Cliente;
use App\Models\User;
use App\Models\EstadoReparacion;
use App\Models\NotaReparacion;
use App\Models\Factura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\FacturaConfiguracion;
use App\Models\SistemaConfiguracion;

class ReparacionesController extends Controller
{
    private const ESTADOS_PENDIENTES = [
        'Recibido',
        'En Diagnóstico',
        'Pendiente Revisión Admin',
        'Esperando Aprobación',
        'Aprobado',
        'Esperando Pieza',
        'En Proceso',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Optimizar consultas: eager loading de todas las relaciones necesarias
        $query = Reparacion::with([
            'equipo:id,cliente_id,marca,modelo,tipo,numero_serie',
            'equipo.cliente:id,nombre,telefono',
            'equipo.fotos:id,equipo_id,ruta',
            'factura:id,reparacion_id,total',
            'tecnico:id,firstname,lastname',
            'recepcionista:id,firstname,lastname'
        ]);

        // Filtro por tipo de servicio
        if ($request->has('tipo_servicio') && $request->tipo_servicio != '') {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }

        // Filtro por tipo de equipo (para GPUs)
        if ($request->has('tipo_equipo') && $request->tipo_equipo == 'gpu') {
            $query->whereHas('equipo', function($q) {
                $q->where('tipo', 'Tarjeta Gráfica (GPU)');
            });
        }

        // Filtro para excluir GPUs (solo reparaciones normales)
        if ($request->has('excluir_gpu') && $request->excluir_gpu == '1') {
            $query->whereHas('equipo', function($q) {
                $q->where('tipo', '!=', 'Tarjeta Gráfica (GPU)');
            });
        }

        // Filtro por estado (incluye filtro especial para "pendientes")
        if ($request->has('estado') && $request->estado != '') {
            if ($request->estado == 'pendientes') {
                // Estados pendientes: todos los que no están finalizados o entregados
                $query->where(function ($subQuery) {
                    foreach (self::ESTADOS_PENDIENTES as $estadoPendiente) {
                        foreach (Reparacion::variantesEstado($estadoPendiente) as $variante) {
                            $subQuery->orWhereRaw('LOWER(estado) = LOWER(?)', [$variante]);
                        }
                    }
                });
            } else {
                $query->whereEstadoNormalizado($request->estado);
            }
        }

        if ($request->has('tecnico_id') && $request->tecnico_id != '') {
            $query->where('tecnico_id', $request->tecnico_id);
        }

        // Filtro por fecha desde y hasta (buscar en fecha_ingreso o created_at)
        // La lógica: (fecha_ingreso en rango) OR (created_at en rango)
        $fechaDesde = $request->has('fecha_desde') && $request->fecha_desde != '' ? $request->fecha_desde : null;
        $fechaHasta = $request->has('fecha_hasta') && $request->fecha_hasta != '' ? $request->fecha_hasta : null;
        
        if ($fechaDesde || $fechaHasta) {
            $query->where(function($q) use ($fechaDesde, $fechaHasta) {
                // Opción 1: fecha_ingreso está en el rango
                $q->where(function($q2) use ($fechaDesde, $fechaHasta) {
                    if ($fechaDesde) {
                        $q2->whereDate('fecha_ingreso', '>=', $fechaDesde);
                    }
                    if ($fechaHasta) {
                        $q2->whereDate('fecha_ingreso', '<=', $fechaHasta);
                    }
                })
                // Opción 2: created_at está en el rango
                ->orWhere(function($q2) use ($fechaDesde, $fechaHasta) {
                    if ($fechaDesde) {
                        $q2->whereDate('created_at', '>=', $fechaDesde);
                    }
                    if ($fechaHasta) {
                        $q2->whereDate('created_at', '<=', $fechaHasta);
                    }
                });
            });
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo_reparacion', 'like', "%{$search}%")
                  ->orWhereHas('equipo.cliente', function($qc) use ($search) {
                      $qc->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro de garantía
        if ($request->has('es_garantia') && $request->es_garantia == '1') {
            $query->where('es_garantia', true);
        }

        // Ordenamiento: por defecto más nueva primero (desc), opción para cambiar a asc
        // Si viene desde dashboard (estado=pendientes), ordenar por más antigua primero (asc) para ver la cola
        $orden = $request->get('orden');
        if (!$orden) {
            $orden = ($request->has('estado') && $request->estado == 'pendientes') ? 'asc' : 'desc';
        }
        $reparaciones = $query->orderBy('fecha_ingreso', $orden)->paginate(10);
        
        // Preservar todos los filtros en la paginación
        $reparaciones->appends($request->query());
        
        // Cachear lista de técnicos
        $tecnicos = Cache::remember('users.tecnicos', 300, function () {
            return User::whereHas('roles', function($q) {
                $q->where('slug', 'tecnico');
            })->get(['id', 'firstname', 'lastname']);
        });

        return view('reparaciones.responsive-index', compact('reparaciones', 'tecnicos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cachear listas que no cambian frecuentemente
        $clientes = Cache::remember('clientes.list', 300, function () {
            return Cliente::orderBy('nombre', 'asc')->get(['id', 'nombre']);
        });
        
        $tecnicos = Cache::remember('users.tecnicos', 300, function () {
            return User::whereHas('roles', function($q) {
                $q->where('slug', 'tecnico');
            })->get(['id', 'firstname', 'lastname']);
        });
        
        $usuarios = Cache::remember('users.all', 300, function () {
            return User::orderBy('firstname', 'asc')->get(['id', 'firstname', 'lastname']);
        });

        $configFactura = FacturaConfiguracion::obtener();
        $porcentajeImpuesto = $configFactura->porcentajeImpuestoActivo();
        $impuestosActivos = $configFactura->impuestosHabilitados();

        return view('reparaciones.create', compact('clientes', 'tecnicos', 'usuarios', 'porcentajeImpuesto', 'impuestosActivos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|string',
            'tipo_personalizado' => 'required_if:tipo,Otro|nullable|string',
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'descripcion_problema' => 'required|string',
            'tipo_servicio' => 'required|in:reparacion,mantenimiento',
            'recepcionista_id' => 'required|exists:users,id',
            'tecnico_id' => 'nullable|exists:users,id',
            'fecha_prometida' => 'nullable|date',
            'precio_cotizado' => 'nullable|numeric|min:0',
            'es_garantia' => 'nullable|boolean',
            'periodo_garantia_dias' => 'nullable|integer|min:1|max:365|required_if:es_garantia,1',
            'reparacion_original_id' => 'nullable|exists:reparaciones,id|required_if:es_garantia,1',
            'fotos.*' => 'nullable|image|max:5120', // 5MB max por foto
        ], [
            'cliente_id.exists' => 'El cliente seleccionado no existe.',
            'recepcionista_id.exists' => 'El personal seleccionado no existe.',
            'tecnico_id.exists' => 'El técnico seleccionado no existe.',
            'tipo_personalizado.required_if' => 'Debe especificar el tipo cuando selecciona "Otro".',
            'periodo_garantia_dias.required_if' => 'El período de garantía es requerido cuando es una reparación en garantía.',
            'reparacion_original_id.required_if' => 'Debe seleccionar la reparación original cuando es una reparación en garantía.',
        ]);

        // Obtener recepcionista antes de la transacción para optimizar
        $recepcionista = User::find($validated['recepcionista_id'], ['id', 'firstname', 'lastname']);
        $recepcionistaNombre = $recepcionista ? $recepcionista->firstname . ' ' . $recepcionista->lastname : 'N/A';
        
        // Obtener configuración de factura antes de la transacción (si es necesario)
        $configFactura = null;
        if ($validated['tipo_servicio'] === 'mantenimiento' && ($validated['precio_cotizado'] ?? null)) {
            $configFactura = FacturaConfiguracion::obtener();
        }

        $aplicarImpuestoCotizacion = $configFactura
            ? $configFactura->debeAplicarImpuesto($request->boolean('aplicar_impuesto'))
            : $request->boolean('aplicar_impuesto');

        // Usar transacción solo para operaciones críticas de base de datos
        $result = DB::transaction(function () use ($validated, $recepcionistaNombre, $configFactura, $aplicarImpuestoCotizacion) {
            // Crear el equipo
            $equipo = Equipo::create([
                'cliente_id' => $validated['cliente_id'],
                'tipo' => $validated['tipo'],
                'tipo_personalizado' => $validated['tipo'] === 'Otro' ? $validated['tipo_personalizado'] : null,
                'marca' => $validated['marca'],
                'modelo' => $validated['modelo'],
                'numero_serie' => $validated['numero_serie'] ?? null,
                'descripcion_problema' => $validated['descripcion_problema'],
                'estado' => 'recibido', // equipos.estado: enum en minúsculas (recibido, diagnostico, reparacion, listo, entregado, garantia)
            ]);

            // Determinar estado inicial según tipo de servicio
            $estadoInicial = 'Recibido';
            if ($validated['tipo_servicio'] === 'mantenimiento') {
                // Para mantenimientos, si hay precio, va directo a proceso
                if ($validated['precio_cotizado'] ?? null) {
                    $estadoInicial = 'En Proceso';
                }
            } else {
                // Para reparaciones, siempre inicia en Recibido
                $estadoInicial = 'Recibido';
            }

            // Calcular total_estimado inicial usando configuración del sistema
            $configSistema = SistemaConfiguracion::obtener();
            $costo_diagnostico = $validated['tipo_servicio'] === 'mantenimiento' 
                ? $configSistema->costo_diagnostico_mantenimiento 
                : $configSistema->costo_diagnostico;
            $costo_piezas = 0;
            $costo_mano_obra = 0;
            $total_estimado = $costo_diagnostico + $costo_piezas + $costo_mano_obra;

            // Calcular fecha de vencimiento de garantía si es garantía
            $fechaVencimientoGarantia = null;
        if (!empty($validated['es_garantia']) && !empty($validated['periodo_garantia_dias'])) {
            $periodoGarantiaDias = (int) $validated['periodo_garantia_dias'];
            $validated['periodo_garantia_dias'] = $periodoGarantiaDias;
            $fechaVencimientoGarantia = Carbon::now()->addDays($periodoGarantiaDias);
        }

            // Generar código de reparación de forma thread-safe
            $ultimoId = Reparacion::lockForUpdate()->max('id') ?? 0;
            $codigoReparacion = 'REP-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
            
            // Verificar que el código no exista (por si acaso)
            while (Reparacion::where('codigo_reparacion', $codigoReparacion)->exists()) {
                $ultimoId++;
                $codigoReparacion = 'REP-' . str_pad($ultimoId + 1, 5, '0', STR_PAD_LEFT);
            }

            // Crear la reparación con lock para prevenir duplicados de código
            $reparacion = Reparacion::create([
                'codigo_reparacion' => $codigoReparacion,
                'equipo_id' => $equipo->id,
                'tecnico_id' => $validated['tecnico_id'] ?? null,
                'recepcionista_id' => $validated['recepcionista_id'],
                'tipo_servicio' => $validated['tipo_servicio'],
                'es_garantia' => !empty($validated['es_garantia']),
                'periodo_garantia_dias' => $validated['periodo_garantia_dias'] ?? null,
                'fecha_vencimiento_garantia' => $fechaVencimientoGarantia,
                'reparacion_original_id' => $validated['reparacion_original_id'] ?? null,
                'estado' => $estadoInicial,
                'fecha_ingreso' => Carbon::now(),
                'fecha_prometida' => $validated['fecha_prometida'] ?? null,
                'costo_diagnostico' => $costo_diagnostico,
                'costo_piezas' => $costo_piezas,
                'costo_mano_obra' => $costo_mano_obra,
                'total_estimado' => $total_estimado,
                'precio_cotizado' => $validated['precio_cotizado'] ?? null,
                'fecha_cotizacion' => $validated['precio_cotizado'] ?? null ? Carbon::now() : null,
                'aplicar_impuesto_cotizacion' => $aplicarImpuestoCotizacion,
            ]);

            // Si es mantenimiento y tiene precio, generar factura inmediatamente
            if ($validated['tipo_servicio'] === 'mantenimiento' && ($validated['precio_cotizado'] ?? null)) {
                $aplicarImpuesto = $configFactura?->debeAplicarImpuesto($aplicarImpuestoCotizacion) ?? $aplicarImpuestoCotizacion;
                $porcentajeImpuesto = $aplicarImpuesto ? ($configFactura?->porcentajeImpuestoActivo() ?? 0) : 0;
                
                $subtotal = $validated['precio_cotizado'];
                $impuestos = $aplicarImpuesto ? (($subtotal * $porcentajeImpuesto) / 100) : 0;
                $total = $subtotal + $impuestos;
                
                $ncf = null;
                $ncfCodigo = SistemaConfiguracion::obtenerNcfCodigo();
                if ($aplicarImpuesto && $ncfCodigo) {
                    $ultimoNCF = Factura::whereNotNull('ncf')->lockForUpdate()->max('id') ?? 0;
                    $ncf = $ncfCodigo . str_pad($ultimoNCF + 1, 8, '0', STR_PAD_LEFT);
                }
                
                // Usar lock para prevenir números de factura duplicados
                $ultimoId = Factura::lockForUpdate()->max('id') ?? 0;
                $numeroFactura = 'FAC-' . date('Y') . '-' . str_pad($ultimoId + 1, 6, '0', STR_PAD_LEFT);
                
                Factura::create([
                    'reparacion_id' => $reparacion->id,
                    'equipo_id' => $equipo->id,
                    'cliente_id' => $equipo->cliente_id,
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

            // Crear primer registro en historial con información detallada
            $comentarioInicial = $validated['tipo_servicio'] === 'mantenimiento' 
                ? "Mantenimiento recibido. Recibido por: {$recepcionistaNombre}. Problema reportado: {$validated['descripcion_problema']}"
                : "Equipo recibido para reparación. Recibido por: {$recepcionistaNombre}. Problema reportado: {$validated['descripcion_problema']}";
            
            if ($validated['fecha_prometida'] ?? null) {
                $fechaPrometida = Carbon::parse($validated['fecha_prometida'])->format('d/m/Y');
                $comentarioInicial .= ". Fecha prometida: {$fechaPrometida}";
            }
            
            EstadoReparacion::create([
                'reparacion_id' => $reparacion->id,
                'estado' => $estadoInicial,
                'comentario' => $comentarioInicial,
                'usuario_id' => auth()->id(),
            ]);
            
            return ['reparacion' => $reparacion, 'equipo' => $equipo];
        });
        
        $reparacion = $result['reparacion'];
        $equipo = $result['equipo'];

        // Subir fotos DESPUÉS de la transacción para no bloquearla
        if ($request->hasFile('fotos')) {
            try {
                $orden = 0;
                foreach ($request->file('fotos') as $foto) {
                    $ruta = $foto->store('equipos/' . $equipo->id, 'public');
                    EquipoFoto::create([
                        'equipo_id' => $equipo->id,
                        'ruta' => $ruta,
                        'nombre_original' => $foto->getClientOriginalName(),
                        'orden' => $orden++,
                    ]);
                }
            } catch (\Exception $e) {
                // Si falla la subida de fotos, registrar error pero no fallar toda la operación
                \Log::error('Error al subir fotos del equipo ' . $equipo->id . ': ' . $e->getMessage());
            }
        }

        // Limpiar caché del dashboard cuando se crea una reparación
        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        Cache::forget('dashboard.total_mantenimientos');
        Cache::forget('dashboard.total_reparaciones');
        Cache::forget('dashboard.total_gpus');
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.reparaciones_por_mes');

        // Redirigir a WhatsApp con el mensaje de entrada (un solo clic para enviar). Si no hay teléfono, ir a la ficha.
        $reparacion->load(['equipo:id,cliente_id,tipo,tipo_personalizado,marca,modelo,numero_serie,descripcion_problema,codigo_unico', 'equipo.cliente:id,nombre,telefono']);
        $urlWhatsApp = $this->buildWhatsAppEntradaUrl($reparacion);
        $sistema = SistemaConfiguracion::obtener();
        $autoImprimir = $sistema->imprimir_etiqueta_auto ?? true;
        $returnUrl = route('reparaciones.etiqueta-entrada', $reparacion);
        $successMessage = ucfirst($validated['tipo_servicio']) . ' creada exitosamente.';

        if ($urlWhatsApp) {
            return $this->redirectWithWhatsApp($urlWhatsApp, $returnUrl, $successMessage, $autoImprimir);
        }

        return redirect()->to($returnUrl)
            ->with('success', $successMessage)
            ->with('autoImprimir', $autoImprimir);
    }

    /**
     * Etiqueta de entrada para imprimir (hoja para pegar al equipo).
     */
    public function etiquetaEntrada(Reparacion $reparacion)
    {
        $reparacion->load(['equipo:id,cliente_id,tipo,tipo_personalizado,marca,modelo,numero_serie,descripcion_problema,codigo_unico', 'equipo.cliente:id,nombre,telefono']);
        $autoImprimir = request()->boolean('autoImprimir', session('autoImprimir', $reparacion->wasRecentlyCreated ?? false));
        return view('reparaciones.etiqueta-entrada', compact('reparacion', 'autoImprimir'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Optimizar: cargar solo campos necesarios para la vista show
        $reparacion = Reparacion::with([
            'equipo:id,cliente_id,tipo,marca,modelo,numero_serie,tipo_personalizado,descripcion_problema,codigo_unico',
            'equipo.cliente:id,nombre,telefono,email',
            'equipo.fotos:id,equipo_id,ruta',
            'tecnico:id,firstname,lastname',
            'tecnicoCompleto:id,firstname,lastname',
            'recepcionista:id,firstname,lastname',
            'cotizacionRevisadaPor:id,firstname,lastname',
            'piezas:id,reparacion_id,nombre,cantidad,precio_unitario',
            'notas:id,reparacion_id,usuario_id,nota,created_at',
            'notas.usuario:id,firstname,lastname',
            'historialEstados:id,reparacion_id,estado,comentario,usuario_id,created_at',
            'historialEstados.usuario:id,firstname,lastname',
            'reparacionOriginal:id,codigo_reparacion,equipo_id,tecnico_id,fecha_finalizacion',
            'reparacionOriginal.equipo:id,cliente_id,marca,modelo',
            'reparacionOriginal.equipo.cliente:id,nombre',
            'reparacionesGarantia:id,reparacion_original_id,codigo_reparacion,estado,fecha_ingreso,fecha_vencimiento_garantia',
            'reparacionesGarantia.equipo:id,cliente_id,marca,modelo',
            'reparacionesGarantia.equipo.cliente:id,nombre'
        ])->findOrFail($id);

        $configFactura = FacturaConfiguracion::obtener();
        $porcentajeImpuesto = $configFactura->porcentajeImpuestoActivo();
        $impuestosActivos = $configFactura->impuestosHabilitados();

        return view('reparaciones.responsive-show', compact('reparacion', 'porcentajeImpuesto', 'impuestosActivos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Optimizar: cargar solo campos necesarios
        $reparacion = Reparacion::with([
            'equipo:id,cliente_id,tipo,marca,modelo,numero_serie,tipo_personalizado',
            'equipo.cliente:id,nombre',
            'reparacionOriginal:id,codigo_reparacion,equipo_id',
            'cotizacionRevisadaPor:id,firstname,lastname',
        ])->findOrFail($id);
        
        // Cachear lista de técnicos
        $tecnicos = Cache::remember('users.tecnicos', 300, function () {
            return User::whereHas('roles', function($q) {
                $q->where('slug', 'tecnico');
            })->get(['id', 'firstname', 'lastname']);
        });

        // Obtener reparaciones finalizadas/entregadas para el selector de reparación original - Optimizado
        $reparacionesOriginales = Cache::remember('reparaciones.originales', 300, function () use ($id) {
            return Reparacion::select('id', 'codigo_reparacion', 'equipo_id', 'fecha_finalizacion')
                ->where(function ($query) {
                    $query->whereEstadoNormalizado('Finalizado')
                        ->orWhere('estado', 'Entregado');
                })
                ->where('id', '!=', $id) // Excluir la reparación actual
                ->with([
                    'equipo:id,cliente_id,marca,modelo',
                    'equipo.cliente:id,nombre'
                ])
                ->orderBy('fecha_finalizacion', 'desc')
                ->limit(100)
                ->get(['id', 'codigo_reparacion', 'equipo_id', 'fecha_finalizacion']);
        });

        $configFactura = FacturaConfiguracion::obtener();
        $porcentajeImpuesto = $configFactura->porcentajeImpuestoActivo();
        $impuestosActivos = $configFactura->impuestosHabilitados();

        return view('reparaciones.edit', compact('reparacion', 'tecnicos', 'reparacionesOriginales', 'porcentajeImpuesto', 'impuestosActivos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reparacion = Reparacion::findOrFail($id);
        $configFactura = FacturaConfiguracion::obtener();
        $aplicarImpuestoCotizacion = $request->has('aplicar_impuesto')
            ? $configFactura->debeAplicarImpuesto($request->boolean('aplicar_impuesto'))
            : $configFactura->debeAplicarImpuesto($reparacion->factura?->aplicar_impuesto ?? $reparacion->aplicar_impuesto_cotizacion ?? false);

        $validated = $request->validate([
            'tecnico_id' => 'nullable|exists:users,id',
            'estado' => 'nullable|string|in:Recibido,En Diagnóstico,Pendiente Revisión Admin,Esperando Aprobación,Aprobado,Esperando Pieza,En Proceso,Finalizado,Sin Reparación,Entregado,Cancelado',
            'fecha_prometida' => 'nullable|date',
            'costo_diagnostico' => 'nullable|numeric|min:0',
            'costo_piezas' => 'nullable|numeric|min:0',
            'costo_mano_obra' => 'nullable|numeric|min:0',
            'precio_cotizado' => 'nullable|numeric|min:0',
            'descripcion_cotizacion' => 'nullable|string|max:2000',
            'comentario' => 'nullable|string|max:500',
            'es_garantia' => 'nullable|boolean',
            'notificar_cliente' => 'nullable|boolean',
            'periodo_garantia_dias' => 'nullable|integer|min:1|max:365|required_if:es_garantia,1',
            'reparacion_original_id' => 'nullable|exists:reparaciones,id|required_if:es_garantia,1',
        ], [
            'periodo_garantia_dias.required_if' => 'El período de garantía es requerido cuando es una reparación en garantía.',
            'reparacion_original_id.required_if' => 'Debe seleccionar la reparación original cuando es una reparación en garantía.',
            'reparacion_original_id.exists' => 'La reparación original seleccionada no existe.',
            'tecnico_id.exists' => 'El técnico seleccionado no existe.',
            'costo_diagnostico.min' => 'El costo de diagnóstico no puede ser negativo.',
            'costo_piezas.min' => 'El costo de piezas no puede ser negativo.',
            'costo_mano_obra.min' => 'El costo de mano de obra no puede ser negativo.',
            'precio_cotizado.min' => 'El precio cotizado no puede ser negativo.',
        ]);

        // Calcular total_estimado si se actualizan los costos
        if (isset($validated['costo_diagnostico']) || isset($validated['costo_piezas']) || isset($validated['costo_mano_obra'])) {
            $costo_diagnostico = $validated['costo_diagnostico'] ?? $reparacion->costo_diagnostico ?? 0;
            $costo_piezas = $validated['costo_piezas'] ?? $reparacion->costo_piezas ?? 0;
            $costo_mano_obra = $validated['costo_mano_obra'] ?? $reparacion->costo_mano_obra ?? 0;
            $validated['total_estimado'] = $costo_diagnostico + $costo_piezas + $costo_mano_obra;
        }

        $usuario = auth()->user();
        $esAdmin = $usuario && $usuario->hasRole('administrador');
        $precioCotizadoActualizado = array_key_exists('precio_cotizado', $validated);
        $precioCotizadoCambio = $precioCotizadoActualizado
            && (float) ($validated['precio_cotizado'] ?? 0) !== (float) ($reparacion->precio_cotizado ?? 0);

        if (!$esAdmin && isset($validated['estado']) && in_array($validated['estado'], ['Esperando Aprobación', 'Aprobado'], true)) {
            return back()
                ->withInput()
                ->with('error', 'Solo un administrador puede enviar una cotización al cliente o marcarla como aprobada.');
        }

        if (($validated['estado'] ?? null) === 'Esperando Aprobación' && (float) ($validated['precio_cotizado'] ?? $reparacion->precio_cotizado ?? 0) <= 0) {
            return back()
                ->withInput()
                ->with('error', 'No puedes enviar una cotización al cliente sin un precio cotizado.');
        }

        // Cuando se modifica la cotización de una reparación, primero pasa por revisión administrativa.
        if ($reparacion->tipo_servicio === 'reparacion' && $precioCotizadoCambio && ($validated['precio_cotizado'] ?? 0) > 0) {
            $validated['estado'] = 'Pendiente Revisión Admin';
            $validated['fecha_cotizacion'] = Carbon::now();
            $validated['cliente_aprobado'] = null;
            $validated['fecha_aprobacion'] = null;
            $validated['cotizacion_revisada_por'] = null;
            $validated['cotizacion_revisada_at'] = null;
        }

        if ($esAdmin && ($validated['estado'] ?? null) === 'Esperando Aprobación' && ($reparacion->tipo_servicio === 'reparacion')) {
            $validated['cotizacion_revisada_por'] = $usuario->id;
            $validated['cotizacion_revisada_at'] = Carbon::now();
            $validated['cliente_aprobado'] = null;
            $validated['fecha_aprobacion'] = null;
        }

        // Guardar si se aplica impuesto a la cotización (para mostrar correctamente en consulta pública)
        $validated['aplicar_impuesto_cotizacion'] = $aplicarImpuestoCotizacion;

        // Si cambió el estado, registrar en historial con información detallada
        $estadoAnterior = $reparacion->estado;
        if (isset($validated['estado']) && $validated['estado'] !== $estadoAnterior) {
            $comentario = $validated['comentario'] ?? $request->comentario ?? '';
            
            // Generar comentario automático según el estado
            if (empty($comentario)) {
                switch ($validated['estado']) {
                    case 'En Diagnóstico':
                        $comentario = 'Equipo en proceso de diagnóstico técnico';
                        break;
                    case 'Pendiente Revisión Admin':
                        $comentario = 'Cotización lista para revisión y confirmación administrativa antes de enviarla al cliente';
                        break;
                    case 'Esperando Aprobación':
                        if ((isset($validated['precio_cotizado']) && $validated['precio_cotizado'] > 0) || $reparacion->precio_cotizado) {
                            $montoCotizado = $validated['precio_cotizado'] ?? $reparacion->precio_cotizado;
                            $comentario = "Cotización enviada al cliente por un monto de $" . number_format($montoCotizado, 2);
                        } else {
                            $comentario = 'Esperando aprobación del cliente';
                        }
                        break;
                    case 'Aprobado':
                        $comentario = 'Cliente aprobó la cotización. Procediendo con la reparación';
                        break;
                    case 'Esperando Pieza':
                        $comentario = 'Esperando llegada de piezas necesarias para la reparación';
                        break;
                    case 'En Proceso':
                        $comentario = 'Reparación en proceso de ejecución';
                        break;
                    case 'Finalizado':
                        $comentario = 'Reparación finalizada. Equipo listo para entrega';
                        break;
                    case 'Sin Reparación':
                        $comentario = 'Diagnóstico finalizado. El equipo no pudo ser reparado.';
                        break;
                    case 'Entregado':
                        $comentario = 'Equipo entregado al cliente';
                        break;
                    case 'Cancelado':
                        $comentario = 'Reparación cancelada';
                        break;
                    default:
                        $comentario = 'Estado actualizado';
                }
            }
            
            EstadoReparacion::create([
                'reparacion_id' => $reparacion->id,
                'estado' => $validated['estado'],
                'comentario' => $comentario,
                'usuario_id' => auth()->id(),
            ]);

            // Si el estado cambia a un cierre operativo, establecer fecha_finalizacion y calcular comisión
            if (in_array($validated['estado'], ['Finalizado', 'Sin Reparación', 'Entregado']) && !$reparacion->fecha_finalizacion) {
                $validated['fecha_finalizacion'] = Carbon::now();
                
                // Calcular comisión si hay un técnico asignado y un monto total
                if ($reparacion->tecnico_id && !$reparacion->tecnico_completo_id) {
                    $validated['tecnico_completo_id'] = $reparacion->tecnico_id;
                    
                    // Obtener porcentaje de comisión de la configuración del sistema
                    $configSistema = SistemaConfiguracion::obtener();
                    $porcentajeComision = $configSistema->porcentaje_comision ?? 10.00;
                    $validated['porcentaje_comision'] = $porcentajeComision;
                    
                    // Calcular monto de comisión basado en el total (precio_cotizado o total_estimado)
                    $montoBase = (float) ($validated['precio_cotizado'] ?? $reparacion->precio_cotizado ?? $validated['total_estimado'] ?? $reparacion->total_estimado ?? 0);
                    if ($montoBase > 0) {
                        $validated['monto_comision'] = ($montoBase * $porcentajeComision) / 100;
                    }
                }
                
                // Si es garantía y tiene período de garantía, calcular fecha de vencimiento
                $esGarantia = isset($validated['es_garantia']) ? !empty($validated['es_garantia']) : $reparacion->es_garantia;
                $periodoGarantia = isset($validated['periodo_garantia_dias']) ? (int) $validated['periodo_garantia_dias'] : (int) $reparacion->periodo_garantia_dias;
                
                if ($esGarantia && $periodoGarantia && !$reparacion->fecha_vencimiento_garantia) {
                    $validated['fecha_vencimiento_garantia'] = Carbon::now()->addDays($periodoGarantia);
                }
                
                // Si hay factura, asegurar que esté actualizada con los valores correctos
                // Prioridad: precio_cotizado (si está aprobado) > precio_cotizado > total_estimado
                if ($reparacion->factura) {
                    $factura = $reparacion->factura;
                    
                    // Determinar el precio final a usar
                    if ($reparacion->cliente_aprobado === true && $reparacion->precio_cotizado) {
                        // Si está aprobado, usar precio_cotizado
                        $precioFinal = (float) ($validated['precio_cotizado'] ?? $reparacion->precio_cotizado);
                    } elseif ($reparacion->precio_cotizado) {
                        // Si hay precio_cotizado pero no está aprobado, usar precio_cotizado
                        $precioFinal = (float) ($validated['precio_cotizado'] ?? $reparacion->precio_cotizado);
                    } else {
                        // Si no hay precio_cotizado, usar total_estimado
                        $precioFinal = (float) ($validated['total_estimado'] ?? $reparacion->total_estimado ?? 0);
                    }
                    
                    $aplicarImpuesto = $configFactura->debeAplicarImpuesto($aplicarImpuestoCotizacion);
                    $porcentajeImpuesto = $aplicarImpuesto ? $configFactura->porcentajeImpuestoActivo() : 0;
                    
                    $subtotal = $precioFinal;
                    $impuestos = $aplicarImpuesto ? (($subtotal * $porcentajeImpuesto) / 100) : 0;
                    $total = $subtotal + $impuestos;
                    
                    $ncf = $factura->ncf;
                    $ncfCodigo = SistemaConfiguracion::obtenerNcfCodigo();
                    if ($aplicarImpuesto && !$ncf && $ncfCodigo) {
                        $ultimoNCF = Factura::whereNotNull('ncf')->lockForUpdate()->max('id') ?? 0;
                        $ncf = $ncfCodigo . str_pad($ultimoNCF + 1, 8, '0', STR_PAD_LEFT);
                    } elseif (!$aplicarImpuesto) {
                        $ncf = null;
                    }
                    
                    // Actualizar factura solo si los valores cambiaron
                    if ($factura->subtotal != $subtotal || $factura->total != $total || $factura->aplicar_impuesto != $aplicarImpuesto || $factura->ncf != $ncf) {
                        $factura->update([
                            'subtotal' => $subtotal,
                            'aplicar_impuesto' => $aplicarImpuesto,
                            'ncf' => $ncf,
                            'impuestos' => $impuestos,
                            'total' => $total,
                        ]);
                    }
                } else {
                    $precioFinal = (float) ($validated['precio_cotizado'] ?? $reparacion->precio_cotizado ?? $validated['total_estimado'] ?? $reparacion->total_estimado ?? 0);
                    if ($precioFinal <= 0) {
                        $precioFinal = (float) ($validated['total_estimado'] ?? $reparacion->total_estimado ?? 0);
                    }

                    if ($precioFinal > 0) {
                        $aplicarImpuesto = $configFactura->debeAplicarImpuesto($aplicarImpuestoCotizacion);
                        $porcentajeImpuesto = $aplicarImpuesto ? $configFactura->porcentajeImpuestoActivo() : 0;

                    $subtotal = $precioFinal;
                    $impuestos = $aplicarImpuesto ? (($subtotal * $porcentajeImpuesto) / 100) : 0;
                    $total = $subtotal + $impuestos;
                    
                    $ncf = null;
                    $ncfCodigo = SistemaConfiguracion::obtenerNcfCodigo();
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
                }
                
                // Agregar registro adicional para fecha de entrega
                if ($validated['estado'] === 'Entregado') {
                    EstadoReparacion::create([
                        'reparacion_id' => $reparacion->id,
                        'estado' => 'Entregado',
                        'comentario' => 'Equipo entregado al cliente el ' . Carbon::now()->format('d/m/Y'),
                        'usuario_id' => auth()->id(),
                    ]);
                }
            }
        }
        
        // Manejar campos de garantía
        if (isset($validated['es_garantia'])) {
            $validated['es_garantia'] = !empty($validated['es_garantia']);
            
            // Si se marca como garantía y tiene período, calcular fecha de vencimiento si no existe
            if ($validated['es_garantia'] && isset($validated['periodo_garantia_dias']) && $validated['periodo_garantia_dias']) {
                $validated['periodo_garantia_dias'] = (int) $validated['periodo_garantia_dias'];
                // Si ya tiene fecha de finalización, calcular desde ahí, sino desde ahora
                $fechaBase = $reparacion->fecha_finalizacion ?? Carbon::now();
                if (!$reparacion->fecha_vencimiento_garantia || isset($validated['periodo_garantia_dias'])) {
                    $validated['fecha_vencimiento_garantia'] = $fechaBase->copy()->addDays($validated['periodo_garantia_dias']);
                }
            } elseif (!$validated['es_garantia']) {
                // Si se desmarca como garantía, limpiar campos relacionados
                $validated['periodo_garantia_dias'] = null;
                $validated['fecha_vencimiento_garantia'] = null;
                $validated['reparacion_original_id'] = null;
            }
        }
        
        // Registrar cambios en costos si se actualizan
        $cambiosCostos = [];
        if (isset($validated['costo_diagnostico']) && $validated['costo_diagnostico'] != $reparacion->costo_diagnostico) {
            $cambiosCostos[] = "Costo de diagnóstico: $" . number_format($reparacion->costo_diagnostico ?? 0, 2) . " → $" . number_format($validated['costo_diagnostico'], 2);
        }
        if (isset($validated['costo_piezas']) && $validated['costo_piezas'] != $reparacion->costo_piezas) {
            $cambiosCostos[] = "Costo de piezas: $" . number_format($reparacion->costo_piezas ?? 0, 2) . " → $" . number_format($validated['costo_piezas'], 2);
        }
        if (isset($validated['costo_mano_obra']) && $validated['costo_mano_obra'] != $reparacion->costo_mano_obra) {
            $cambiosCostos[] = "Costo de mano de obra: $" . number_format($reparacion->costo_mano_obra ?? 0, 2) . " → $" . number_format($validated['costo_mano_obra'], 2);
        }
        
        if (!empty($cambiosCostos) && !isset($validated['estado'])) {
            EstadoReparacion::create([
                'reparacion_id' => $reparacion->id,
                'estado' => $reparacion->estado,
                'comentario' => 'Actualización de costos: ' . implode(', ', $cambiosCostos),
                'usuario_id' => auth()->id(),
            ]);
        }

        $reparacion->update($validated);
        $this->sincronizarEstadoEquipo($reparacion);

        // Limpiar caché del dashboard cuando se actualiza una reparación
        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        Cache::forget('dashboard.stats');

        $redirectUrl = route('reparaciones.show', $reparacion);
        $successMessage = 'Reparación actualizada exitosamente';
        $debeNotificarCliente = $request->boolean('notificar_cliente') && !$this->estadoEsInterno($reparacion->estado);

        if ($debeNotificarCliente) {
            $urlWhatsApp = $this->buildWhatsAppEstadoUrl($reparacion);
            if ($urlWhatsApp) {
                return $this->redirectWithWhatsApp($urlWhatsApp, $redirectUrl, $successMessage, null, true);
            }

            return redirect()->to($redirectUrl)
                ->with('success', $successMessage)
                ->with('info', 'La reparación se actualizó, pero el cliente no tiene teléfono registrado para enviar el aviso.');
        }

        return redirect()->to($redirectUrl)
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $reparacion = Reparacion::findOrFail($id);
        $reparacion->delete();

        // Limpiar caché del dashboard cuando se elimina una reparación
        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        Cache::forget('dashboard.total_mantenimientos');
        Cache::forget('dashboard.total_reparaciones');
        Cache::forget('dashboard.total_gpus');
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.reparaciones_por_mes');

        return redirect()->route('reparaciones.index')
            ->with('success', 'Reparación eliminada exitosamente');
    }

    /**
     * Permitir que un técnico se asigne a sí mismo un trabajo disponible.
     * Siempre se asigna al usuario autenticado actual (nunca desde el request).
     */
    public function tomarTrabajo(Request $request, string $id)
    {
        $reparacion = Reparacion::findOrFail($id);

        // Usar solo el usuario autenticado actual; ignorar cualquier dato del request
        $userId = auth()->id();
        if (!$userId) {
            return back()->with('error', 'Debes iniciar sesión para tomar un trabajo.');
        }

        $usuario = User::find($userId);
        if (!$usuario) {
            return back()->with('error', 'Usuario no encontrado.');
        }

        if (!$usuario->hasRole('tecnico') && !$usuario->hasRole('administrador')) {
            return back()->with('error', 'Solo los técnicos pueden tomar trabajos.');
        }

        if ($reparacion->tecnico_id && $reparacion->tecnico_id != $userId) {
            return back()->with('error', 'Este trabajo ya está asignado a otro técnico.');
        }

        // Asignar exclusivamente al usuario conectado
        $reparacion->tecnico_id = $userId;
        $reparacion->save();

        EstadoReparacion::create([
            'reparacion_id' => $reparacion->id,
            'estado' => $reparacion->estado,
            'comentario' => "Trabajo tomado por {$usuario->firstname} {$usuario->lastname}",
            'usuario_id' => $userId,
        ]);
        
        // Limpiar caché
        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        
        return back()->with('success', 'Trabajo asignado exitosamente. ¡Buena suerte!');
    }

    /**
     * Marcar un trabajo como completado y calcular comisión
     */
    public function completarTrabajo(Request $request, string $id)
    {
        $reparacion = Reparacion::findOrFail($id);
        $usuario = auth()->user();
        
        // Verificar que el usuario sea técnico o administrador
        if (!$usuario->hasRole('tecnico') && !$usuario->hasRole('administrador')) {
            return back()->with('error', 'Solo los técnicos pueden completar trabajos.');
        }
        
        // Verificar que el trabajo esté asignado al técnico actual (o que sea admin)
        if (!$usuario->hasRole('administrador') && $reparacion->tecnico_id != $usuario->id) {
            return back()->with('error', 'Este trabajo no está asignado a ti.');
        }
        
        // Verificar que el trabajo no esté ya completado
        if (in_array($reparacion->estado, ['Finalizado', 'Sin Reparación', 'Entregado'])) {
            return back()->with('error', 'Este trabajo ya está completado.');
        }
        
        // Obtener configuración para porcentaje de comisión del sistema
        $configSistema = SistemaConfiguracion::obtener();
        $porcentajeComision = $configSistema->porcentaje_comision ?? 10.00;
        
        // Calcular monto de comisión basado en el total
        $montoBase = $reparacion->precio_cotizado ?? $reparacion->total_estimado ?? 0;
        $montoComision = $montoBase > 0 ? ($montoBase * $porcentajeComision) / 100 : 0;
        
        // Actualizar reparación
        $reparacion->estado = 'Finalizado';
        $reparacion->fecha_finalizacion = Carbon::now();
        $reparacion->tecnico_completo_id = $reparacion->tecnico_id ?? $usuario->id;
        $reparacion->porcentaje_comision = $porcentajeComision;
        $reparacion->monto_comision = $montoComision;
        $reparacion->save();
        $this->sincronizarEstadoEquipo($reparacion);
        
        // Registrar en historial
        EstadoReparacion::create([
            'reparacion_id' => $reparacion->id,
            'estado' => 'Finalizado',
            'comentario' => "Trabajo completado por {$usuario->firstname} {$usuario->lastname}. Comisión: {$porcentajeComision}% ($" . number_format($montoComision, 2) . ")",
            'usuario_id' => $usuario->id,
        ]);
        
        // Limpiar caché
        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        Cache::forget('dashboard.stats');
        
        return back()->with('success', "Trabajo completado exitosamente. Comisión calculada: $" . number_format($montoComision, 2) . " ({$porcentajeComision}%)");
    }

    /**
     * Revisar y enviar una cotización al cliente desde un administrador.
     */
    public function enviarCotizacionCliente(Request $request, Reparacion $reparacion)
    {
        $usuario = auth()->user();
        if (!$usuario || !$usuario->hasRole('administrador')) {
            abort(403, 'Solo un administrador puede enviar cotizaciones al cliente.');
        }

        if ($reparacion->tipo_servicio !== 'reparacion') {
            return back()->with('error', 'Este flujo aplica solo a reparaciones.');
        }

        if (!$reparacion->precio_cotizado || $reparacion->precio_cotizado <= 0) {
            return back()->with('error', 'Debes registrar un precio cotizado antes de enviar la cotización al cliente.');
        }

        $reparacion->update([
            'estado' => 'Esperando Aprobación',
            'fecha_cotizacion' => $reparacion->fecha_cotizacion ?? Carbon::now(),
            'cliente_aprobado' => null,
            'fecha_aprobacion' => null,
            'cotizacion_revisada_por' => $usuario->id,
            'cotizacion_revisada_at' => Carbon::now(),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion->id,
            'estado' => 'Esperando Aprobación',
            'comentario' => 'Cotización revisada por administración y enviada al cliente para su aprobación.',
            'usuario_id' => $usuario->id,
        ]);

        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        Cache::forget('dashboard.stats');

        $redirectUrl = route('reparaciones.show', $reparacion);
        $successMessage = 'Cotización revisada y enviada al cliente.';
        $urlWhatsApp = $this->buildWhatsAppEstadoUrl($reparacion);

        if ($urlWhatsApp) {
            return $this->redirectWithWhatsApp($urlWhatsApp, $redirectUrl, $successMessage, null, true);
        }

        return redirect()->to($redirectUrl)
            ->with('success', $successMessage)
            ->with('info', 'La cotización quedó enviada, pero el cliente no tiene teléfono registrado para notificarle por WhatsApp.');
    }

    /**
     * Abrir WhatsApp con los detalles de entrada del equipo para enviar al cliente.
     * La foto no se incluye; el usuario puede adjuntarla manualmente si desea.
     * Mantener este botón por si falla el envío al crear o para reenviar más tarde.
     */
    public function compartirEntradaWhatsApp(Reparacion $reparacion)
    {
        $reparacion->load(['equipo:id,cliente_id,tipo,tipo_personalizado,marca,modelo,numero_serie,descripcion_problema,codigo_unico', 'equipo.cliente:id,nombre,telefono']);
        $urlWhatsApp = $this->buildWhatsAppEntradaUrl($reparacion);
        if (!$urlWhatsApp) {
            return back()->with('error', 'El cliente no tiene un número de teléfono registrado para enviar por WhatsApp.');
        }
        return redirect()->away($urlWhatsApp);
    }

    /**
     * Abrir WhatsApp con un aviso del estado actual para el cliente.
     */
    public function compartirEstadoWhatsApp(Reparacion $reparacion)
    {
        $reparacion->load([
            'equipo:id,cliente_id,tipo,tipo_personalizado,marca,modelo,numero_serie,descripcion_problema,codigo_unico',
            'equipo.cliente:id,nombre,telefono',
        ]);

        if ($this->estadoEsInterno($reparacion->estado)) {
            return back()->with('error', 'Ese estado es interno y no debe notificarse al cliente.');
        }

        $urlWhatsApp = $this->buildWhatsAppEstadoUrl($reparacion);
        if (!$urlWhatsApp) {
            return back()->with('error', 'El cliente no tiene un número de teléfono registrado para enviar el aviso por WhatsApp.');
        }

        return redirect()->away($urlWhatsApp);
    }

    /**
     * Construir URL de WhatsApp con mensaje de entrada del equipo. Devuelve null si el cliente no tiene teléfono.
     */
    private function buildWhatsAppEntradaUrl(Reparacion $reparacion): ?string
    {
        $equipo = $reparacion->equipo;
        $cliente = $equipo->cliente ?? null;
        if (!$cliente || empty(trim($cliente->telefono ?? ''))) {
            return null;
        }

        $configuracion = FacturaConfiguracion::obtener();
        $empresaNombre = $configuracion->empresa_nombre ?? 'Martinez Devices';
        if (stripos($empresaNombre, 'MartinezService') !== false) {
            $empresaNombre = 'Martinez Devices';
        }

        $tipoEquipo = $equipo->tipo . ($equipo->tipo_personalizado ? " ({$equipo->tipo_personalizado})" : '');
        $fechaIngreso = $reparacion->fecha_ingreso ? $reparacion->fecha_ingreso->format('d/m/Y') : 'N/A';
        $fechaPrometida = $reparacion->fecha_prometida ? $reparacion->fecha_prometida->format('d/m/Y') : 'N/A';

        $urlConsulta = route('public.consulta', ['codigo' => $equipo->codigo_unico]);

        $mensaje = "Hola {$cliente->nombre},\n\n";
        $mensaje .= "Te confirmamos la *entrada de tu equipo* en {$empresaNombre}.\n\n";
        $mensaje .= "*Detalles de entrada:*\n";
        $mensaje .= "• Tipo: {$tipoEquipo}\n";
        $mensaje .= "• Marca: {$equipo->marca}\n";
        $mensaje .= "• Modelo: {$equipo->modelo}\n";
        if ($equipo->numero_serie) {
            $mensaje .= "• Nº Serie: {$equipo->numero_serie}\n";
        }
        $mensaje .= "• Problema reportado: {$equipo->descripcion_problema}\n";
        $mensaje .= "• Fecha de ingreso: {$fechaIngreso}\n";
        $mensaje .= "• Fecha prometida: {$fechaPrometida}\n\n";
        $mensaje .= "*Código del equipo:* {$equipo->codigo_unico}\n";
        $mensaje .= "*Código de reparación:* {$reparacion->codigo_reparacion}\n\n";
        $mensaje .= "Puedes consultar el estado de tu equipo en:\n{$urlConsulta}\n\n";
        $mensaje .= "Guarda este mensaje para futuras consultas.";

        $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono);
        return 'https://wa.me/' . $telefono . '?text=' . rawurlencode($mensaje);
    }

    /**
     * Construir URL de WhatsApp con el estado actual de la reparación.
     */
    private function buildWhatsAppEstadoUrl(Reparacion $reparacion): ?string
    {
        $reparacion->loadMissing([
            'equipo:id,cliente_id,tipo,tipo_personalizado,marca,modelo,codigo_unico',
            'equipo.cliente:id,nombre,telefono',
        ]);

        $cliente = $reparacion->equipo->cliente ?? null;
        if (!$cliente || empty(trim($cliente->telefono ?? ''))) {
            return null;
        }

        $configuracion = FacturaConfiguracion::obtener();
        $empresaNombre = $configuracion->empresa_nombre ?? 'Martinez Devices';
        if (stripos($empresaNombre, 'MartinezService') !== false) {
            $empresaNombre = 'Martinez Devices';
        }

        $tipoEquipo = $reparacion->equipo->tipo . ($reparacion->equipo->tipo_personalizado ? " ({$reparacion->equipo->tipo_personalizado})" : '');
        $urlConsulta = route('public.consulta', ['codigo' => $reparacion->equipo->codigo_unico]);
        $mensajeEstado = match ($reparacion->estado) {
            'Esperando Aprobación' => "Tu equipo está *esperando tu aprobación* para continuar con la reparación.\nConfirma aquí: {$urlConsulta}",
            'Aprobado' => 'Tu cotización fue aprobada y ya estamos avanzando con la reparación.',
            'En Diagnóstico' => 'Tu equipo está actualmente en diagnóstico técnico.',
            'Esperando Pieza' => 'Tu equipo está a la espera de una pieza para poder continuar.',
            'En Proceso' => 'Tu reparación ya está en proceso.',
            'Finalizado' => 'Tu equipo ya fue reparado y está listo para entrega.',
            'Sin Reparación' => 'El diagnóstico de tu equipo ha finalizado. Lamentablemente el equipo no pudo ser reparado.',
            'Entregado' => 'Tu equipo ya fue entregado. Gracias por preferirnos.',
            'Cancelado' => 'El proceso de reparación fue cancelado.',
            default => "El estado actual de tu equipo es: *{$reparacion->estado}*.",
        };

        $mensaje = "Hola {$cliente->nombre},\n\n";
        $mensaje .= "Te escribimos desde {$empresaNombre} para actualizarte sobre tu equipo.\n\n";
        $mensaje .= "*Equipo:* {$tipoEquipo}\n";
        $mensaje .= "*Código de reparación:* {$reparacion->codigo_reparacion}\n";
        $mensaje .= "*Estado actual:* {$reparacion->estado}\n\n";
        $mensaje .= $mensajeEstado . "\n\n";
        $mensaje .= "Puedes consultar el detalle completo aquí:\n{$urlConsulta}";

        $telefono = preg_replace('/[^0-9]/', '', $cliente->telefono);
        return 'https://wa.me/' . $telefono . '?text=' . rawurlencode($mensaje);
    }

    private function estadoEsInterno(?string $estado): bool
    {
        return in_array($estado, ['Pendiente Revisión Admin'], true);
    }

    private function redirectWithWhatsApp(string $urlWhatsApp, string $returnUrl, string $successMessage, ?bool $autoImprimir = null, bool $openInSameTab = false)
    {
        if ($autoImprimir !== null) {
            $separator = str_contains($returnUrl, '?') ? '&' : '?';
            $returnUrl .= $separator . 'autoImprimir=' . ($autoImprimir ? '1' : '0');
        }

        return response()->view('reparaciones.whatsapp-redirect', [
            'urlWhatsApp' => $urlWhatsApp,
            'returnUrl' => $returnUrl,
            'successMessage' => $successMessage,
            'autoImprimir' => $autoImprimir,
            'openInSameTab' => $openInSameTab,
        ]);
    }

    private function sincronizarEstadoEquipo(Reparacion $reparacion): void
    {
        $reparacion->loadMissing('equipo');

        if (!$reparacion->equipo) {
            return;
        }

        $estadoEquipo = match ($reparacion->estado) {
            'Recibido' => 'recibido',
            'En DiagnÃ³stico' => 'diagnostico',
            'Pendiente RevisiÃ³n Admin',
            'Esperando AprobaciÃ³n',
            'Aprobado',
            'Esperando Pieza',
            'En Proceso' => $reparacion->es_garantia ? 'garantia' : 'reparacion',
            'Finalizado', 'Sin Reparación' => 'listo',
            'Entregado' => 'entregado',
            default => $reparacion->equipo->estado,
        };

        if ($reparacion->equipo->estado !== $estadoEquipo) {
            $reparacion->equipo->estado = $estadoEquipo;
            $reparacion->equipo->save();
        }
    }

    /**
     * Agregar nota a una reparación
     */
    public function addNota(Request $request, $id)
    {
        $request->validate([
            'nota' => 'required|string',
        ]);

        NotaReparacion::create([
            'reparacion_id' => $id,
            'usuario_id' => auth()->id(),
            'nota' => $request->nota,
        ]);

        return back()->with('success', 'Nota agregada exitosamente');
    }

    public function updateHistorialComentario(Request $request, Reparacion $reparacion, EstadoReparacion $historial)
    {
        $usuario = auth()->user();
        if (!$usuario || !$usuario->hasRole('administrador')) {
            abort(403, 'Solo un administrador puede editar comentarios del historial.');
        }

        if ((int) $historial->reparacion_id !== (int) $reparacion->id) {
            abort(404);
        }

        $validated = $request->validate([
            'comentario' => 'required|string|max:500',
        ]);

        $historial->comentario = $validated['comentario'];
        $historial->save();

        return back()->with('success', 'Comentario del historial actualizado exitosamente.');
    }

    public function destroyHistorialComentario(Reparacion $reparacion, EstadoReparacion $historial)
    {
        $usuario = auth()->user();
        if (!$usuario || !$usuario->hasRole('administrador')) {
            abort(403, 'Solo un administrador puede eliminar comentarios del historial.');
        }

        if ((int) $historial->reparacion_id !== (int) $reparacion->id) {
            abort(404);
        }

        $historial->comentario = null;
        $historial->save();

        return back()->with('success', 'Comentario del historial eliminado exitosamente.');
    }
}
