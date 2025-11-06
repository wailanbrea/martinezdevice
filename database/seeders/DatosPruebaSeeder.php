<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Rol;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Reparacion;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Garantia;
use App\Models\HistorialEstado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatosPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Creando datos de prueba...');

        // Obtener roles
        $rolAdmin = Rol::where('slug', 'administrador')->first();
        $rolTecnico = Rol::where('slug', 'tecnico')->first();
        $rolRecepcion = Rol::where('slug', 'recepcion')->first();
        $rolContabilidad = Rol::where('slug', 'contabilidad')->first();

        // Crear usuarios adicionales
        $this->command->info('👥 Creando usuarios...');
        
        $usuarios = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@martinezservice.com',
                'password' => Hash::make('password'),
                'rol' => $rolTecnico,
            ],
            [
                'name' => 'María García',
                'email' => 'maria.garcia@martinezservice.com',
                'password' => Hash::make('password'),
                'rol' => $rolTecnico,
            ],
            [
                'name' => 'Carlos Rodríguez',
                'email' => 'carlos.rodriguez@martinezservice.com',
                'password' => Hash::make('password'),
                'rol' => $rolRecepcion,
            ],
            [
                'name' => 'Ana Martínez',
                'email' => 'ana.martinez@martinezservice.com',
                'password' => Hash::make('password'),
                'rol' => $rolContabilidad,
            ],
        ];

        $usuariosCreados = [];
        foreach ($usuarios as $usuarioData) {
            $usuario = User::firstOrCreate(
                ['email' => $usuarioData['email']],
                [
                    'name' => $usuarioData['name'],
                    'password' => $usuarioData['password'],
                ]
            );
            $usuario->roles()->syncWithoutDetaching([$usuarioData['rol']->id]);
            $usuariosCreados[] = $usuario;
        }

        $tecnicos = [$usuariosCreados[0], $usuariosCreados[1]]; // Juan y María son técnicos
        $recepcion = $usuariosCreados[2]; // Carlos es recepción
        $contabilidad = $usuariosCreados[3]; // Ana es contabilidad

        // Crear clientes
        $this->command->info('👤 Creando clientes...');
        
        $clientes = [
            [
                'nombre' => 'Roberto Sánchez',
                'cedula_rnc' => '001-1234567-8',
                'telefono' => '809-555-0101',
                'correo' => 'roberto.sanchez@email.com',
                'direccion' => 'Av. Winston Churchill #123, Santo Domingo',
            ],
            [
                'nombre' => 'Laura Fernández',
                'cedula_rnc' => '001-2345678-9',
                'telefono' => '809-555-0102',
                'correo' => 'laura.fernandez@email.com',
                'direccion' => 'Calle Las Mercedes #45, Santiago',
            ],
            [
                'nombre' => 'Empresa Tech Solutions SRL',
                'cedula_rnc' => '131-123456-7',
                'telefono' => '809-555-0103',
                'correo' => 'contacto@techsolutions.com',
                'direccion' => 'Zona Industrial Herrera, Santo Domingo',
            ],
            [
                'nombre' => 'Miguel Torres',
                'cedula_rnc' => '001-3456789-0',
                'telefono' => '829-555-0104',
                'correo' => 'miguel.torres@email.com',
                'direccion' => 'Av. Independencia #789, La Romana',
            ],
            [
                'nombre' => 'Sofía Ramírez',
                'cedula_rnc' => '001-4567890-1',
                'telefono' => '849-555-0105',
                'correo' => 'sofia.ramirez@email.com',
                'direccion' => 'Calle Duarte #321, San Pedro de Macorís',
            ],
        ];

        $clientesCreados = [];
        foreach ($clientes as $clienteData) {
            $cliente = Cliente::firstOrCreate(
                ['cedula_rnc' => $clienteData['cedula_rnc']],
                $clienteData
            );
            $clientesCreados[] = $cliente;
        }

        // Crear equipos
        $this->command->info('💻 Creando equipos...');
        
        $equiposData = [
            [
                'cliente' => $clientesCreados[0],
                'tipo' => 'Laptop',
                'marca' => 'Dell',
                'modelo' => 'Inspiron 15 3000',
                'numero_serie' => 'DL123456789',
                'descripcion_falla' => 'No enciende, posible problema con la fuente de alimentación',
                'estado' => 'reparacion',
                'fecha_recibido' => Carbon::now()->subDays(5),
            ],
            [
                'cliente' => $clientesCreados[0],
                'tipo' => 'PC',
                'marca' => 'HP',
                'modelo' => 'Pavilion Desktop',
                'numero_serie' => 'HP987654321',
                'descripcion_falla' => 'Pantalla azul al iniciar, necesita reinstalación de Windows',
                'estado' => 'listo',
                'fecha_recibido' => Carbon::now()->subDays(10),
            ],
            [
                'cliente' => $clientesCreados[1],
                'tipo' => 'Laptop',
                'marca' => 'Lenovo',
                'modelo' => 'ThinkPad E14',
                'numero_serie' => 'LN456789123',
                'descripcion_falla' => 'Teclado no funciona, necesita reemplazo',
                'estado' => 'diagnostico',
                'fecha_recibido' => Carbon::now()->subDays(2),
            ],
            [
                'cliente' => $clientesCreados[1],
                'tipo' => 'GPU',
                'marca' => 'NVIDIA',
                'modelo' => 'RTX 3060',
                'numero_serie' => 'NV789123456',
                'descripcion_falla' => 'Sobrecalentamiento, necesita limpieza y cambio de pasta térmica',
                'estado' => 'reparacion',
                'fecha_recibido' => Carbon::now()->subDays(7),
            ],
            [
                'cliente' => $clientesCreados[2],
                'tipo' => 'Laptop',
                'marca' => 'ASUS',
                'modelo' => 'ROG Strix G15',
                'numero_serie' => 'AS321654987',
                'descripcion_falla' => 'Pantalla rota, necesita reemplazo de panel',
                'estado' => 'listo',
                'fecha_recibido' => Carbon::now()->subDays(12),
            ],
            [
                'cliente' => $clientesCreados[2],
                'tipo' => 'PC',
                'marca' => 'Custom Build',
                'modelo' => 'Gaming PC',
                'numero_serie' => 'CB159753468',
                'descripcion_falla' => 'No arranca, posible falla en placa madre',
                'estado' => 'diagnostico',
                'fecha_recibido' => Carbon::now()->subDays(1),
            ],
            [
                'cliente' => $clientesCreados[3],
                'tipo' => 'Laptop',
                'marca' => 'Acer',
                'modelo' => 'Aspire 5',
                'numero_serie' => 'AC987654321',
                'descripcion_falla' => 'Batería no carga, necesita reemplazo',
                'estado' => 'entregado',
                'fecha_recibido' => Carbon::now()->subDays(20),
            ],
            [
                'cliente' => $clientesCreados[4],
                'tipo' => 'Consola',
                'marca' => 'PlayStation',
                'modelo' => 'PS5',
                'numero_serie' => 'PS123456789',
                'descripcion_falla' => 'No lee discos, necesita limpieza del lector',
                'estado' => 'reparacion',
                'fecha_recibido' => Carbon::now()->subDays(3),
            ],
        ];

        $equiposCreados = [];
        foreach ($equiposData as $equipoData) {
            $codigoUnico = 'EQ-' . strtoupper(Str::random(8));
            
            // Autenticar como recepción para que el historial se cree con el usuario correcto
            auth()->login($recepcion);
            
            $equipo = Equipo::create([
                'cliente_id' => $equipoData['cliente']->id,
                'tipo' => $equipoData['tipo'],
                'marca' => $equipoData['marca'],
                'modelo' => $equipoData['modelo'],
                'numero_serie' => $equipoData['numero_serie'],
                'descripcion_falla' => $equipoData['descripcion_falla'],
                'estado' => 'recibido', // Siempre empezar como recibido
                'codigo_unico' => $codigoUnico,
                'created_at' => $equipoData['fecha_recibido'],
                'updated_at' => $equipoData['fecha_recibido'],
            ]);
            
            // Actualizar el historial creado automáticamente
            $historialInicial = HistorialEstado::where('equipo_id', $equipo->id)->first();
            if ($historialInicial) {
                $historialInicial->update([
                    'usuario_id' => $recepcion->id,
                    'observaciones' => 'Equipo recibido en recepción',
                    'created_at' => $equipoData['fecha_recibido'],
                ]);
            }

            // Si el estado final es diferente a 'recibido', actualizar el equipo y crear historial
            if ($equipoData['estado'] !== 'recibido') {
                $equipo->update([
                    'estado' => $equipoData['estado'],
                    'updated_at' => $equipoData['fecha_recibido']->copy()->addHours(2),
                ]);

                // Actualizar el último historial creado por el evento updated
                $historialCambio = HistorialEstado::where('equipo_id', $equipo->id)
                    ->where('estado_nuevo', $equipoData['estado'])
                    ->latest()
                    ->first();
                
                if ($historialCambio) {
                    $historialCambio->update([
                        'estado_anterior' => 'recibido',
                        'usuario_id' => $recepcion->id,
                        'observaciones' => 'Cambio de estado automático',
                        'created_at' => $equipoData['fecha_recibido']->copy()->addHours(2),
                    ]);
                }
            }
            
            $equiposCreados[] = $equipo;
        }
        
        // Cerrar sesión
        auth()->logout();

        // Crear reparaciones
        $this->command->info('🔧 Creando reparaciones...');
        
        $reparacionesData = [
            [
                'equipo' => $equiposCreados[0], // Laptop Dell
                'tecnico' => $tecnicos[0],
                'diagnostico' => 'Fuente de alimentación dañada, requiere reemplazo',
                'piezas_reemplazadas' => ['Fuente de alimentación 19V 3.42A'],
                'costo_mano_obra' => 1500.00,
                'costo_piezas' => 2500.00,
                'fecha_inicio' => Carbon::now()->subDays(4),
                'estado' => 'en_proceso',
            ],
            [
                'equipo' => $equiposCreados[1], // PC HP
                'tecnico' => $tecnicos[1],
                'diagnostico' => 'Windows corrupto, reinstalación completa del sistema operativo',
                'piezas_reemplazadas' => [],
                'costo_mano_obra' => 2000.00,
                'costo_piezas' => 0.00,
                'fecha_inicio' => Carbon::now()->subDays(8),
                'fecha_finalizacion' => Carbon::now()->subDays(1),
                'estado' => 'completada',
            ],
            [
                'equipo' => $equiposCreados[3], // GPU NVIDIA
                'tecnico' => $tecnicos[0],
                'diagnostico' => 'Pasta térmica seca y acumulación de polvo, limpieza completa realizada',
                'piezas_reemplazadas' => ['Pasta térmica premium'],
                'costo_mano_obra' => 800.00,
                'costo_piezas' => 500.00,
                'fecha_inicio' => Carbon::now()->subDays(5),
                'estado' => 'en_proceso',
            ],
            [
                'equipo' => $equiposCreados[4], // Laptop ASUS
                'tecnico' => $tecnicos[1],
                'diagnostico' => 'Panel LCD roto, reemplazado con panel original',
                'piezas_reemplazadas' => ['Panel LCD 15.6" Full HD'],
                'costo_mano_obra' => 1800.00,
                'costo_piezas' => 8500.00,
                'fecha_inicio' => Carbon::now()->subDays(10),
                'fecha_finalizacion' => Carbon::now()->subDays(2),
                'estado' => 'completada',
            ],
            [
                'equipo' => $equiposCreados[6], // Laptop Acer
                'tecnico' => $tecnicos[0],
                'diagnostico' => 'Batería agotada, reemplazada con batería original',
                'piezas_reemplazadas' => ['Batería Li-ion 4400mAh'],
                'costo_mano_obra' => 600.00,
                'costo_piezas' => 3200.00,
                'fecha_inicio' => Carbon::now()->subDays(18),
                'fecha_finalizacion' => Carbon::now()->subDays(15),
                'estado' => 'completada',
            ],
            [
                'equipo' => $equiposCreados[7], // PlayStation 5
                'tecnico' => $tecnicos[1],
                'diagnostico' => 'Lector de discos sucio, limpieza profunda realizada',
                'piezas_reemplazadas' => [],
                'costo_mano_obra' => 1200.00,
                'costo_piezas' => 0.00,
                'fecha_inicio' => Carbon::now()->subDays(2),
                'estado' => 'en_proceso',
            ],
        ];

        $reparacionesCreadas = [];
        foreach ($reparacionesData as $reparacionData) {
            $reparacion = Reparacion::create([
                'equipo_id' => $reparacionData['equipo']->id,
                'tecnico_id' => $reparacionData['tecnico']->id,
                'diagnostico' => $reparacionData['diagnostico'],
                'piezas_reemplazadas' => $reparacionData['piezas_reemplazadas'],
                'costo_mano_obra' => $reparacionData['costo_mano_obra'],
                'costo_piezas' => $reparacionData['costo_piezas'],
                'total' => $reparacionData['costo_mano_obra'] + $reparacionData['costo_piezas'],
                'fecha_inicio' => $reparacionData['fecha_inicio'],
                'fecha_finalizacion' => $reparacionData['fecha_finalizacion'] ?? null,
                'estado' => $reparacionData['estado'],
                'created_at' => $reparacionData['fecha_inicio'],
                'updated_at' => $reparacionData['fecha_finalizacion'] ?? Carbon::now(),
            ]);
            $reparacionesCreadas[] = $reparacion;
        }

        // Crear facturas
        $this->command->info('🧾 Creando facturas...');
        
        $facturaNumero = 1;
        $facturasCreadas = [];

        foreach ($reparacionesCreadas as $index => $reparacion) {
            if ($reparacion->estado === 'completada') {
                $subtotal = $reparacion->total;
                $impuestos = $subtotal * 0.18; // 18% ITBIS
                $total = $subtotal + $impuestos;

                $numeroFactura = 'FAC-' . str_pad($facturaNumero++, 6, '0', STR_PAD_LEFT);
                $factura = Factura::create([
                    'numero_factura' => $numeroFactura,
                    'cliente_id' => $reparacion->equipo->cliente_id,
                    'equipo_id' => $reparacion->equipo_id,
                    'reparacion_id' => $reparacion->id,
                    'subtotal' => $subtotal,
                    'impuestos' => $impuestos,
                    'total' => $total,
                    'forma_pago' => ['efectivo', 'transferencia', 'tarjeta'][rand(0, 2)],
                    'fecha_emision' => $reparacion->fecha_finalizacion ?? Carbon::now()->subDays(rand(1, 5)),
                    'created_at' => $reparacion->fecha_finalizacion ?? Carbon::now()->subDays(rand(1, 5)),
                ]);
                $facturasCreadas[] = $factura;
            }
        }

        // Crear pagos
        $this->command->info('💳 Creando pagos...');
        
        foreach ($facturasCreadas as $factura) {
            // Algunas facturas pagadas completamente, otras parcialmente
            $montoTotal = $factura->total;
            $montoPagado = rand(0, 1) ? $montoTotal : $montoTotal * 0.5; // 50% o 100%

            Pago::create([
                'factura_id' => $factura->id,
                'monto' => $montoPagado,
                'tipo_pago' => $factura->forma_pago,
                'usuario_id' => $contabilidad->id,
                'fecha' => $factura->fecha_emision->addDays(rand(0, 2)),
                'observaciones' => $montoPagado < $montoTotal ? 'Pago parcial' : 'Pago completo',
                'created_at' => $factura->fecha_emision,
            ]);
        }

        // Crear garantías
        $this->command->info('🛡️ Creando garantías...');
        
        $garantiasCreadas = 0;
        foreach ($reparacionesCreadas as $index => $reparacion) {
            if ($reparacion->estado === 'completada' && $reparacion->fecha_finalizacion) {
                // Crear garantía para las primeras 2 reparaciones completadas (para asegurar que se creen)
                if ($garantiasCreadas < 2 || rand(0, 1)) {
                    $duracionDias = 30;
                    $fechaInicio = $reparacion->fecha_finalizacion;
                    $fechaVencimiento = $fechaInicio->copy()->addDays($duracionDias);
                    
                    Garantia::create([
                        'equipo_id' => $reparacion->equipo_id,
                        'reparacion_id' => $reparacion->id,
                        'fecha_inicio' => $fechaInicio,
                        'fecha_vencimiento' => $fechaVencimiento,
                        'duracion_dias' => $duracionDias,
                        'descripcion' => 'Garantía de reparación por 30 días',
                        'activa' => $fechaVencimiento->isFuture(),
                        'created_at' => $fechaInicio,
                    ]);
                    $garantiasCreadas++;
                }
            }
        }

        $this->command->info('✅ Datos de prueba creados exitosamente!');
        $this->command->info('📊 Resumen:');
        $this->command->info('   - Usuarios: ' . User::count());
        $this->command->info('   - Clientes: ' . Cliente::count());
        $this->command->info('   - Equipos: ' . Equipo::count());
        $this->command->info('   - Reparaciones: ' . Reparacion::count());
        $this->command->info('   - Facturas: ' . Factura::count());
        $this->command->info('   - Pagos: ' . Pago::count());
        $this->command->info('   - Garantías: ' . Garantia::count());
    }
}

