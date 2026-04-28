<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\EquipoFoto;
use App\Models\Reparacion;
use App\Models\EstadoReparacion;
use App\Models\Pieza;
use App\Models\NotaReparacion;
use App\Models\Factura;
use App\Models\FacturaConfiguracion;
use App\Models\SistemaConfiguracion;
use App\Models\User;
use Carbon\Carbon;

class LimpiezaCompletaSeeder extends Seeder
{
    /**
     * Limpiar base de datos y crear 2 registros completos y coherentes
     */
    public function run(): void
    {
        $this->command->info('🧹 Limpiando base de datos...');

        // Limpiar en orden inverso de dependencias (compatible con SQLite y MySQL)
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
        }
        
        // Usar DELETE en lugar de TRUNCATE para compatibilidad con SQLite
        DB::table('pagos')->delete();
        DB::table('facturas')->delete();
        DB::table('notas_reparacion')->delete();
        DB::table('piezas')->delete();
        DB::table('estados_reparacion')->delete();
        DB::table('reparaciones')->delete();
        DB::table('equipo_fotos')->delete();
        DB::table('equipos')->delete();
        DB::table('clientes')->delete();
        
        // Resetear secuencias (solo para SQLite)
        if ($driver === 'sqlite') {
            DB::statement('DELETE FROM sqlite_sequence WHERE name IN ("pagos", "facturas", "notas_reparacion", "piezas", "estados_reparacion", "reparaciones", "equipo_fotos", "equipos", "clientes")');
            DB::statement('PRAGMA foreign_keys=ON');
        } else {
            // Para MySQL, resetear auto_increment
            DB::statement('ALTER TABLE pagos AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE facturas AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE notas_reparacion AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE piezas AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE estados_reparacion AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE reparaciones AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE equipo_fotos AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE equipos AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE clientes AUTO_INCREMENT = 1');
        }

        // Limpiar archivos de fotos (opcional, comentado para no borrar archivos reales)
        // Storage::disk('public')->deleteDirectory('equipos');

        $this->command->info('✅ Base de datos limpiada');
        $this->command->info('📦 Creando 2 registros completos...');

        // Obtener usuarios existentes (no los tocamos)
        $admin = User::first();
        $tecnico = User::whereHas('roles', function($q) {
            $q->where('slug', 'tecnico');
        })->first() ?? $admin;
        $recepcionista = $admin;

        // ============================================
        // REGISTRO 1: REPARACIÓN COMPLETA (FLUJO COMPLETO)
        // ============================================
        $this->command->info('📝 Creando Registro 1: Reparación completa...');

        $cliente1 = Cliente::create([
            'nombre' => 'Juan Pérez López',
            'cedula_rnc' => '001-2345678-9',
            'telefono' => '+1 (829) 555-0456',
            'email' => 'juan.perez@email.com',
            'direccion' => 'Av. 27 de Febrero #123, Santiago, República Dominicana',
        ]);

        $equipo1 = Equipo::create([
            'cliente_id' => $cliente1->id,
            'tipo' => 'Laptop',
            'marca' => 'HP',
            'modelo' => 'Pavilion Gaming 15',
            'numero_serie' => 'HP-PAV-2024-001',
            'descripcion_problema' => 'La laptop no enciende, hace ruidos extraños al intentar arrancar. El cliente reporta que se apagó de repente mientras trabajaba.',
            'estado' => 'listo',
        ]);

        // Crear foto de ejemplo para el equipo 1
        try {
            $rutaFoto1 = 'equipos/' . $equipo1->id . '/equipo-1.jpg';
            $rutaOrigen = public_path('storage/equipos/4/gpu-real.jpg');
            
            if (file_exists($rutaOrigen)) {
                Storage::disk('public')->put($rutaFoto1, file_get_contents($rutaOrigen));
            } else {
                // Crear directorio si no existe
                Storage::disk('public')->makeDirectory('equipos/' . $equipo1->id);
                // Crear imagen placeholder simple
                $imagen = imagecreatetruecolor(400, 300);
                $fondo = imagecolorallocate($imagen, 240, 240, 240);
                imagefill($imagen, 0, 0, $fondo);
                $texto = imagecolorallocate($imagen, 100, 100, 100);
                imagestring($imagen, 5, 150, 140, 'Equipo 1', $texto);
                imagejpeg($imagen, storage_path('app/public/' . $rutaFoto1), 80);
                imagedestroy($imagen);
            }
            
            EquipoFoto::create([
                'equipo_id' => $equipo1->id,
                'ruta' => $rutaFoto1,
                'nombre_original' => 'equipo-1.jpg',
                'orden' => 0,
            ]);
        } catch (\Exception $e) {
            $this->command->warn('No se pudo crear foto para equipo 1: ' . $e->getMessage());
        }

        $reparacion1 = Reparacion::create([
            'equipo_id' => $equipo1->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'estado' => 'Finalizado',
            'tipo_servicio' => 'reparacion',
            'es_garantia' => false,
            'fecha_ingreso' => Carbon::now()->subDays(15),
            'fecha_prometida' => Carbon::now()->subDays(8),
            'fecha_finalizacion' => Carbon::now()->subDays(5),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 235.00,
            'costo_mano_obra' => 100.00,
            'total_estimado' => 385.00,
            'precio_cotizado' => 400.00,
            'descripcion_cotizacion' => 'Problema detectado: La fuente de alimentación presenta fallos intermitentes y el disco duro muestra sectores dañados. Se requiere reemplazo de ambos componentes.

Piezas necesarias:
- Fuente de alimentación HP original 90W
- Disco duro SSD 500GB SATA

Trabajos a realizar:
- Reemplazo de fuente de alimentación
- Migración de datos del disco antiguo al nuevo SSD
- Instalación de sistema operativo y drivers
- Pruebas de estabilidad y rendimiento',
            'cliente_aprobado' => true,
            'fecha_cotizacion' => Carbon::now()->subDays(12),
            'fecha_aprobacion' => Carbon::now()->subDays(11),
        ]);

        // Estados de la reparación 1
        EstadoReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido para reparación. Recibido por: ' . $recepcionista->firstname . ' ' . $recepcionista->lastname . '. Problema reportado: La laptop no enciende, hace ruidos extraños al intentar arrancar.',
            'usuario_id' => $recepcionista->id,
            'created_at' => $reparacion1->fecha_ingreso,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'estado' => 'En Diagnóstico',
            'comentario' => 'Realizando diagnóstico completo del equipo. Se detectaron problemas en fuente de alimentación y disco duro.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(13),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'estado' => 'Esperando Aprobación',
            'comentario' => 'Cotización enviada al cliente por un monto de $400.00',
            'usuario_id' => $tecnico->id,
            'created_at' => $reparacion1->fecha_cotizacion,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'estado' => 'Aprobado',
            'comentario' => 'Cotización aprobada por el cliente. El técnico puede proceder con la reparación.',
            'usuario_id' => $admin->id,
            'created_at' => $reparacion1->fecha_aprobacion,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'estado' => 'En Proceso',
            'comentario' => 'Iniciando reparación. Se procederá con el reemplazo de componentes.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(10),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'estado' => 'Finalizado',
            'comentario' => 'Reparación completada exitosamente. Equipo probado y funcionando correctamente.',
            'usuario_id' => $tecnico->id,
            'created_at' => $reparacion1->fecha_finalizacion,
        ]);

        // Piezas utilizadas
        Pieza::create([
            'reparacion_id' => $reparacion1->id,
            'nombre' => 'Fuente de Alimentación HP 90W',
            'descripcion' => 'Fuente original HP para Pavilion Gaming 15',
            'cantidad' => 1,
            'precio_unitario' => 150.00,
            'precio_total' => 150.00,
        ]);

        Pieza::create([
            'reparacion_id' => $reparacion1->id,
            'nombre' => 'SSD 500GB SATA',
            'descripcion' => 'Disco duro sólido Kingston A400',
            'cantidad' => 1,
            'precio_unitario' => 85.00,
            'precio_total' => 85.00,
        ]);

        // Nota del técnico
        NotaReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'usuario_id' => $tecnico->id,
            'nota' => 'Migración de datos completada exitosamente. El cliente puede recoger el equipo.',
            'created_at' => Carbon::now()->subDays(5),
        ]);

        // Factura (sin impuesto)
        $factura1 = Factura::create([
            'reparacion_id' => $reparacion1->id,
            'equipo_id' => $equipo1->id,
            'cliente_id' => $cliente1->id,
            'numero_factura' => 'FAC-' . date('Y') . '-000001',
            'fecha_emision' => $reparacion1->fecha_finalizacion,
            'subtotal' => 400.00,
            'aplicar_impuesto' => false,
            'ncf' => null,
            'impuestos' => 0.00,
            'total' => 400.00,
            'forma_pago' => 'efectivo',
        ]);

        $this->command->info('✅ Registro 1 creado: REP-' . str_pad($reparacion1->id, 5, '0', STR_PAD_LEFT));

        // ============================================
        // REGISTRO 2: MANTENIMIENTO COMPLETO (CON IMPUESTO)
        // ============================================
        $this->command->info('📝 Creando Registro 2: Mantenimiento completo...');

        $cliente2 = Cliente::create([
            'nombre' => 'María Rodríguez',
            'cedula_rnc' => '402-3456789-0',
            'telefono' => '+1 (849) 555-0789',
            'email' => 'maria.rodriguez@email.com',
            'direccion' => 'Calle Duarte #67, La Vega, República Dominicana',
        ]);

        $equipo2 = Equipo::create([
            'cliente_id' => $cliente2->id,
            'tipo' => 'Tarjeta Gráfica (GPU)',
            'marca' => 'NVIDIA',
            'modelo' => 'GeForce RTX 3080',
            'numero_serie' => 'SN-54321-ABC',
            'descripcion_problema' => 'La tarjeta gráfica presenta sobrecalentamiento y fallos de renderizado durante juegos. Los ventiladores giran al máximo constantemente.',
            'estado' => 'entregado',
        ]);

        // Crear foto de ejemplo para el equipo 2
        try {
            $rutaFoto2 = 'equipos/' . $equipo2->id . '/equipo-2.jpg';
            $rutaOrigen = public_path('storage/equipos/4/gpu-real.jpg');
            
            if (file_exists($rutaOrigen)) {
                Storage::disk('public')->put($rutaFoto2, file_get_contents($rutaOrigen));
            } else {
                // Crear directorio si no existe
                Storage::disk('public')->makeDirectory('equipos/' . $equipo2->id);
                // Crear imagen placeholder simple
                $imagen = imagecreatetruecolor(400, 300);
                $fondo = imagecolorallocate($imagen, 240, 240, 240);
                imagefill($imagen, 0, 0, $fondo);
                $texto = imagecolorallocate($imagen, 100, 100, 100);
                imagestring($imagen, 5, 150, 140, 'Equipo 2', $texto);
                imagejpeg($imagen, storage_path('app/public/' . $rutaFoto2), 80);
                imagedestroy($imagen);
            }
            
            EquipoFoto::create([
                'equipo_id' => $equipo2->id,
                'ruta' => $rutaFoto2,
                'nombre_original' => 'equipo-2.jpg',
                'orden' => 0,
            ]);
        } catch (\Exception $e) {
            $this->command->warn('No se pudo crear foto para equipo 2: ' . $e->getMessage());
        }

        $reparacion2 = Reparacion::create([
            'equipo_id' => $equipo2->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'estado' => 'Entregado',
            'tipo_servicio' => 'mantenimiento',
            'es_garantia' => false,
            'fecha_ingreso' => Carbon::now()->subDays(8),
            'fecha_prometida' => Carbon::now()->subDays(5),
            'fecha_finalizacion' => Carbon::now()->subDays(3),
            'costo_diagnostico' => 0.00,
            'costo_piezas' => 280.00,
            'costo_mano_obra' => 150.00,
            'total_estimado' => 430.00,
            'precio_cotizado' => 750.00,
            'descripcion_cotizacion' => 'Problema detectado: La tarjeta gráfica presenta sobrecalentamiento y fallos de renderizado durante juegos y aplicaciones de alto rendimiento. Se detectaron temperaturas anómalas en el chip VRAM.

Piezas necesarias:
- Chip VRAM de reemplazo (GDDR6X)
- Pasta térmica de alta conductividad térmica
- Almohadillas térmicas nuevas para los módulos de memoria

Trabajos a realizar:
- Reemplazo del chip VRAM dañado
- Limpieza completa del disipador y ventiladores
- Aplicación de nueva pasta térmica en el GPU
- Reemplazo de almohadillas térmicas en todos los módulos VRAM
- Pruebas de estabilidad y temperatura bajo carga
- Verificación de rendimiento y benchmarks',
            'cliente_aprobado' => true,
            'fecha_cotizacion' => Carbon::now()->subDays(7),
            'fecha_aprobacion' => Carbon::now()->subDays(6),
        ]);

        // Estados de la reparación 2
        EstadoReparacion::create([
            'reparacion_id' => $reparacion2->id,
            'estado' => 'Recibido',
            'comentario' => 'Mantenimiento recibido. Recibido por: ' . $recepcionista->firstname . ' ' . $recepcionista->lastname . '. Problema reportado: Sobrecalentamiento y fallos de renderizado.',
            'usuario_id' => $recepcionista->id,
            'created_at' => $reparacion2->fecha_ingreso,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion2->id,
            'estado' => 'En Diagnóstico',
            'comentario' => 'Realizando pruebas de voltaje y temperatura en la GPU.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(7),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion2->id,
            'estado' => 'En Proceso',
            'comentario' => 'Iniciando mantenimiento. Limpieza y reemplazo de componentes térmicos.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(6),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion2->id,
            'estado' => 'Finalizado',
            'comentario' => 'Mantenimiento completado. GPU funcionando correctamente, temperaturas normales.',
            'usuario_id' => $tecnico->id,
            'created_at' => $reparacion2->fecha_finalizacion,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion2->id,
            'estado' => 'Entregado',
            'comentario' => 'Equipo entregado al cliente el ' . Carbon::now()->subDays(2)->format('d/m/Y'),
            'usuario_id' => $recepcionista->id,
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // Piezas utilizadas
        Pieza::create([
            'reparacion_id' => $reparacion2->id,
            'nombre' => 'Chip VRAM GDDR6X',
            'descripcion' => 'Chip de memoria VRAM de reemplazo',
            'cantidad' => 1,
            'precio_unitario' => 200.00,
            'precio_total' => 200.00,
        ]);

        Pieza::create([
            'reparacion_id' => $reparacion2->id,
            'nombre' => 'Pasta Térmica Premium',
            'descripcion' => 'Pasta térmica de alta conductividad térmica',
            'cantidad' => 1,
            'precio_unitario' => 30.00,
            'precio_total' => 30.00,
        ]);

        Pieza::create([
            'reparacion_id' => $reparacion2->id,
            'nombre' => 'Almohadillas Térmicas',
            'descripcion' => 'Almohadillas térmicas para módulos VRAM',
            'cantidad' => 1,
            'precio_unitario' => 50.00,
            'precio_total' => 50.00,
        ]);

        // Nota del técnico
        NotaReparacion::create([
            'reparacion_id' => $reparacion2->id,
            'usuario_id' => $tecnico->id,
            'nota' => 'Mantenimiento completado exitosamente. Temperaturas bajo carga: GPU 72°C, VRAM 78°C. Rendimiento óptimo.',
            'created_at' => $reparacion2->fecha_finalizacion,
        ]);

        // Factura (CON impuesto y NCF)
        $configFactura = FacturaConfiguracion::obtener();
        $porcentajeImpuesto = (float) ($configFactura->impuesto_porcentaje ?? 18.00);
        $subtotal2 = 750.00;
        $impuestos2 = ($subtotal2 * $porcentajeImpuesto) / 100;
        $total2 = $subtotal2 + $impuestos2;
        
        // Generar NCF
        $ncf = null;
        $ncfCodigo = SistemaConfiguracion::obtenerNcfCodigo();
        if ($ncfCodigo) {
            $ultimoNCF2 = Factura::whereNotNull('ncf')->lockForUpdate()->max('id') ?? 0;
            $ncf = $ncfCodigo . str_pad($ultimoNCF2 + 1, 8, '0', STR_PAD_LEFT);
        }

        $factura2 = Factura::create([
            'reparacion_id' => $reparacion2->id,
            'equipo_id' => $equipo2->id,
            'cliente_id' => $cliente2->id,
            'numero_factura' => 'FAC-' . date('Y') . '-000002',
            'fecha_emision' => $reparacion2->fecha_finalizacion,
            'subtotal' => $subtotal2,
            'aplicar_impuesto' => true,
            'ncf' => $ncf,
            'impuestos' => $impuestos2,
            'total' => $total2,
            'forma_pago' => 'efectivo',
        ]);

        $this->command->info('✅ Registro 2 creado: REP-' . str_pad($reparacion2->id, 5, '0', STR_PAD_LEFT));

        // ============================================
        // REGISTRO 3: GARANTÍA (RELACIONADA CON REPARACIÓN 1)
        // ============================================
        $this->command->info('📝 Creando Registro 3: Garantía...');

        // Usar el mismo cliente y equipo de la reparación 1
        $reparacion3 = Reparacion::create([
            'equipo_id' => $equipo1->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'estado' => 'En Diagnóstico',
            'tipo_servicio' => 'reparacion',
            'es_garantia' => true,
            'reparacion_original_id' => $reparacion1->id,
            'periodo_garantia_dias' => 90,
            'fecha_vencimiento_garantia' => $reparacion1->fecha_finalizacion->copy()->addDays(90),
            'fecha_ingreso' => Carbon::now()->subDays(2),
            'fecha_prometida' => Carbon::now()->addDays(3),
            'costo_diagnostico' => 0.00,
            'costo_piezas' => 0.00,
            'costo_mano_obra' => 0.00,
            'total_estimado' => 0.00,
            'precio_cotizado' => null,
            'descripcion_cotizacion' => null,
            'cliente_aprobado' => null,
        ]);

        // Estados de la garantía
        EstadoReparacion::create([
            'reparacion_id' => $reparacion3->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido por garantía. Reparación original: ' . $reparacion1->codigo_reparacion . '. Recibido por: ' . $recepcionista->firstname . ' ' . $recepcionista->lastname . '. Problema reportado: El equipo presenta el mismo problema que se reparó anteriormente.',
            'usuario_id' => $recepcionista->id,
            'created_at' => $reparacion3->fecha_ingreso,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion3->id,
            'estado' => 'En Diagnóstico',
            'comentario' => 'Realizando diagnóstico para verificar si el problema está cubierto por la garantía. Verificando si es el mismo componente reparado anteriormente.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        // Nota del técnico
        NotaReparacion::create([
            'reparacion_id' => $reparacion3->id,
            'usuario_id' => $tecnico->id,
            'nota' => 'Equipo en garantía. Verificando si el fallo está relacionado con la reparación original. La garantía vence el ' . $reparacion3->fecha_vencimiento_garantia->format('d/m/Y') . '.',
            'created_at' => Carbon::now()->subDays(1),
        ]);

        $this->command->info('✅ Registro 3 creado: REP-' . str_pad($reparacion3->id, 5, '0', STR_PAD_LEFT) . ' (Garantía de ' . $reparacion1->codigo_reparacion . ')');

        // ============================================
        // REGISTRO 4: MANTENIMIENTO CON NCF
        // ============================================
        $this->command->info('📝 Creando Registro 4: Mantenimiento con NCF...');

        $cliente3 = Cliente::create([
            'nombre' => 'Carlos Martínez',
            'cedula_rnc' => '001-5678901-2',
            'telefono' => '+1 (809) 555-1234',
            'email' => 'carlos.martinez@email.com',
            'direccion' => 'Av. Winston Churchill #456, Santo Domingo, República Dominicana',
        ]);

        $equipo3 = Equipo::create([
            'cliente_id' => $cliente3->id,
            'tipo' => 'Laptop',
            'marca' => 'Dell',
            'modelo' => 'XPS 15',
            'numero_serie' => 'DELL-XPS-2024-001',
            'descripcion_problema' => 'Limpieza general y mantenimiento preventivo. El cliente solicita revisión completa del sistema.',
            'estado' => 'entregado',
        ]);

        // Crear foto de ejemplo para el equipo 3
        try {
            $rutaFoto3 = 'equipos/' . $equipo3->id . '/equipo-3.jpg';
            $rutaOrigen = public_path('storage/equipos/4/gpu-real.jpg');
            
            if (file_exists($rutaOrigen)) {
                Storage::disk('public')->put($rutaFoto3, file_get_contents($rutaOrigen));
            } else {
                Storage::disk('public')->makeDirectory('equipos/' . $equipo3->id);
                $imagen = imagecreatetruecolor(400, 300);
                $fondo = imagecolorallocate($imagen, 240, 240, 240);
                imagefill($imagen, 0, 0, $fondo);
                $texto = imagecolorallocate($imagen, 100, 100, 100);
                imagestring($imagen, 5, 150, 140, 'Equipo 3', $texto);
                imagejpeg($imagen, storage_path('app/public/' . $rutaFoto3), 80);
                imagedestroy($imagen);
            }
            
            EquipoFoto::create([
                'equipo_id' => $equipo3->id,
                'ruta' => $rutaFoto3,
                'nombre_original' => 'equipo-3.jpg',
                'orden' => 0,
            ]);
        } catch (\Exception $e) {
            $this->command->warn('No se pudo crear foto para equipo 3: ' . $e->getMessage());
        }

        $reparacion4 = Reparacion::create([
            'equipo_id' => $equipo3->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'estado' => 'Entregado',
            'tipo_servicio' => 'mantenimiento',
            'es_garantia' => false,
            'fecha_ingreso' => Carbon::now()->subDays(5),
            'fecha_prometida' => Carbon::now()->subDays(2),
            'fecha_finalizacion' => Carbon::now()->subDays(1),
            'costo_diagnostico' => 0.00,
            'costo_piezas' => 50.00,
            'costo_mano_obra' => 100.00,
            'total_estimado' => 150.00,
            'precio_cotizado' => 200.00,
            'descripcion_cotizacion' => 'Mantenimiento preventivo completo.

Piezas necesarias:
- Pasta térmica de alta calidad
- Limpiador de componentes

Trabajos a realizar:
- Limpieza completa interna y externa
- Aplicación de nueva pasta térmica
- Optimización del sistema operativo
- Verificación de componentes
- Pruebas de rendimiento',
            'cliente_aprobado' => true,
            'fecha_cotizacion' => Carbon::now()->subDays(4),
            'fecha_aprobacion' => Carbon::now()->subDays(4),
        ]);

        // Estados de la reparación 4
        EstadoReparacion::create([
            'reparacion_id' => $reparacion4->id,
            'estado' => 'Recibido',
            'comentario' => 'Mantenimiento recibido. Recibido por: ' . $recepcionista->firstname . ' ' . $recepcionista->lastname . '. Problema reportado: Limpieza general y mantenimiento preventivo.',
            'usuario_id' => $recepcionista->id,
            'created_at' => $reparacion4->fecha_ingreso,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion4->id,
            'estado' => 'En Proceso',
            'comentario' => 'Iniciando mantenimiento preventivo.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(4),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion4->id,
            'estado' => 'Finalizado',
            'comentario' => 'Mantenimiento completado exitosamente.',
            'usuario_id' => $tecnico->id,
            'created_at' => $reparacion4->fecha_finalizacion,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion4->id,
            'estado' => 'Entregado',
            'comentario' => 'Equipo entregado al cliente el ' . Carbon::now()->format('d/m/Y'),
            'usuario_id' => $recepcionista->id,
            'created_at' => Carbon::now()->subDays(1),
        ]);

        // Piezas utilizadas
        Pieza::create([
            'reparacion_id' => $reparacion4->id,
            'nombre' => 'Pasta Térmica Premium',
            'descripcion' => 'Pasta térmica de alta calidad',
            'cantidad' => 1,
            'precio_unitario' => 30.00,
            'precio_total' => 30.00,
        ]);

        Pieza::create([
            'reparacion_id' => $reparacion4->id,
            'nombre' => 'Limpiador de Componentes',
            'descripcion' => 'Limpiador especializado para componentes electrónicos',
            'cantidad' => 1,
            'precio_unitario' => 20.00,
            'precio_total' => 20.00,
        ]);

        // Nota del técnico
        NotaReparacion::create([
            'reparacion_id' => $reparacion4->id,
            'usuario_id' => $tecnico->id,
            'nota' => 'Mantenimiento completado. Equipo optimizado y funcionando correctamente.',
            'created_at' => $reparacion4->fecha_finalizacion,
        ]);

        // Factura (CON impuesto y NCF)
        $subtotal4 = 200.00;
        $impuestos4 = ($subtotal4 * $porcentajeImpuesto) / 100;
        $total4 = $subtotal4 + $impuestos4;
        
        // Generar NCF
        $ncf4 = null;
        if ($ncfCodigo) {
            $ultimoNCF4 = Factura::whereNotNull('ncf')->lockForUpdate()->max('id') ?? 0;
            $ncf4 = $ncfCodigo . str_pad($ultimoNCF4 + 1, 8, '0', STR_PAD_LEFT);
        }

        $factura4 = Factura::create([
            'reparacion_id' => $reparacion4->id,
            'equipo_id' => $equipo3->id,
            'cliente_id' => $cliente3->id,
            'numero_factura' => 'FAC-' . date('Y') . '-000003',
            'fecha_emision' => $reparacion4->fecha_finalizacion,
            'subtotal' => $subtotal4,
            'aplicar_impuesto' => true,
            'ncf' => $ncf4,
            'impuestos' => $impuestos4,
            'total' => $total4,
            'forma_pago' => 'efectivo',
        ]);

        $this->command->info('✅ Registro 4 creado: REP-' . str_pad($reparacion4->id, 5, '0', STR_PAD_LEFT));

        // ============================================
        // REGISTRO 5: GPU CON NCF
        // ============================================
        $this->command->info('📝 Creando Registro 5: GPU con NCF...');

        $cliente4 = Cliente::create([
            'nombre' => 'Ana García',
            'cedula_rnc' => '402-1234567-8',
            'telefono' => '+1 (809) 555-0123',
            'email' => 'ana.garcia@email.com',
            'direccion' => 'Calle Principal #45, Santo Domingo, República Dominicana',
        ]);

        $equipo4 = Equipo::create([
            'cliente_id' => $cliente4->id,
            'tipo' => 'Tarjeta Gráfica (GPU)',
            'marca' => 'AMD',
            'modelo' => 'Radeon RX 6800 XT',
            'numero_serie' => 'AMD-RX-6800XT-001',
            'descripcion_problema' => 'La GPU presenta artefactos visuales y cuelgues durante juegos. Temperaturas elevadas.',
            'estado' => 'listo',
        ]);

        // Crear foto de ejemplo para el equipo 4
        try {
            $rutaFoto4 = 'equipos/' . $equipo4->id . '/equipo-4.jpg';
            $rutaOrigen = public_path('storage/equipos/4/gpu-real.jpg');
            
            if (file_exists($rutaOrigen)) {
                Storage::disk('public')->put($rutaFoto4, file_get_contents($rutaOrigen));
            } else {
                Storage::disk('public')->makeDirectory('equipos/' . $equipo4->id);
                $imagen = imagecreatetruecolor(400, 300);
                $fondo = imagecolorallocate($imagen, 240, 240, 240);
                imagefill($imagen, 0, 0, $fondo);
                $texto = imagecolorallocate($imagen, 100, 100, 100);
                imagestring($imagen, 5, 150, 140, 'Equipo 4', $texto);
                imagejpeg($imagen, storage_path('app/public/' . $rutaFoto4), 80);
                imagedestroy($imagen);
            }
            
            EquipoFoto::create([
                'equipo_id' => $equipo4->id,
                'ruta' => $rutaFoto4,
                'nombre_original' => 'equipo-4.jpg',
                'orden' => 0,
            ]);
        } catch (\Exception $e) {
            $this->command->warn('No se pudo crear foto para equipo 4: ' . $e->getMessage());
        }

        $reparacion5 = Reparacion::create([
            'equipo_id' => $equipo4->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'estado' => 'En Proceso',
            'tipo_servicio' => 'reparacion',
            'es_garantia' => false,
            'fecha_ingreso' => Carbon::now()->subDays(3),
            'fecha_prometida' => Carbon::now()->addDays(2),
            'fecha_finalizacion' => null,
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 150.00,
            'costo_mano_obra' => 200.00,
            'total_estimado' => 400.00,
            'precio_cotizado' => 500.00,
            'descripcion_cotizacion' => 'Problema detectado: La GPU presenta artefactos visuales y cuelgues durante juegos. Se detectaron temperaturas anómalas y posible problema en el sistema de refrigeración.

Piezas necesarias:
- Pasta térmica de alta conductividad
- Almohadillas térmicas nuevas
- Limpieza profunda del disipador

Trabajos a realizar:
- Desmontaje completo de la GPU
- Limpieza profunda del disipador y ventiladores
- Reemplazo de pasta térmica
- Reemplazo de almohadillas térmicas
- Pruebas de estabilidad y temperatura
- Verificación de rendimiento',
            'cliente_aprobado' => true,
            'fecha_cotizacion' => Carbon::now()->subDays(8),
            'fecha_aprobacion' => Carbon::now()->subDays(7),
        ]);

        // Estados de la reparación 5
        EstadoReparacion::create([
            'reparacion_id' => $reparacion5->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido para reparación. Recibido por: ' . $recepcionista->firstname . ' ' . $recepcionista->lastname . '. Problema reportado: Artefactos visuales y cuelgues durante juegos.',
            'usuario_id' => $recepcionista->id,
            'created_at' => $reparacion5->fecha_ingreso,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion5->id,
            'estado' => 'En Diagnóstico',
            'comentario' => 'Realizando diagnóstico completo de la GPU.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(9),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion5->id,
            'estado' => 'Esperando Aprobación',
            'comentario' => 'Cotización enviada al cliente por un monto de $500.00',
            'usuario_id' => $tecnico->id,
            'created_at' => $reparacion5->fecha_cotizacion,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion5->id,
            'estado' => 'Aprobado',
            'comentario' => 'Cotización aprobada por el cliente. El técnico puede proceder con la reparación.',
            'usuario_id' => $admin->id,
            'created_at' => $reparacion5->fecha_aprobacion,
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion5->id,
            'estado' => 'En Proceso',
            'comentario' => 'Iniciando reparación de la GPU.',
            'usuario_id' => $tecnico->id,
            'created_at' => Carbon::now()->subDays(6),
        ]);

        // No crear estado "Finalizado" para que aparezca en el dashboard como pendiente

        // Piezas utilizadas
        Pieza::create([
            'reparacion_id' => $reparacion5->id,
            'nombre' => 'Pasta Térmica Premium',
            'descripcion' => 'Pasta térmica de alta conductividad térmica',
            'cantidad' => 1,
            'precio_unitario' => 40.00,
            'precio_total' => 40.00,
        ]);

        Pieza::create([
            'reparacion_id' => $reparacion5->id,
            'nombre' => 'Almohadillas Térmicas',
            'descripcion' => 'Almohadillas térmicas para memoria VRAM',
            'cantidad' => 1,
            'precio_unitario' => 60.00,
            'precio_total' => 60.00,
        ]);

        Pieza::create([
            'reparacion_id' => $reparacion5->id,
            'nombre' => 'Kit de Limpieza',
            'descripcion' => 'Kit completo de limpieza para componentes',
            'cantidad' => 1,
            'precio_unitario' => 50.00,
            'precio_total' => 50.00,
        ]);

        // Nota del técnico
        NotaReparacion::create([
            'reparacion_id' => $reparacion5->id,
            'usuario_id' => $tecnico->id,
            'nota' => 'GPU en proceso de reparación. Aplicando pasta térmica y almohadillas nuevas.',
            'created_at' => Carbon::now()->subDays(2),
        ]);

        // Factura (CON impuesto y NCF) - Se crea aunque esté en proceso porque el usuario lo solicitó
        $subtotal5 = 500.00;
        $impuestos5 = ($subtotal5 * $porcentajeImpuesto) / 100;
        $total5 = $subtotal5 + $impuestos5;
        
        // Generar NCF
        $ncf5 = null;
        if ($ncfCodigo) {
            $ultimoNCF5 = Factura::whereNotNull('ncf')->lockForUpdate()->max('id') ?? 0;
            $ncf5 = $ncfCodigo . str_pad($ultimoNCF5 + 1, 8, '0', STR_PAD_LEFT);
        }

        $factura5 = Factura::create([
            'reparacion_id' => $reparacion5->id,
            'equipo_id' => $equipo4->id,
            'cliente_id' => $cliente4->id,
            'numero_factura' => 'FAC-' . date('Y') . '-000004',
            'fecha_emision' => Carbon::now()->subDays(2),
            'subtotal' => $subtotal5,
            'aplicar_impuesto' => true,
            'ncf' => $ncf5,
            'impuestos' => $impuestos5,
            'total' => $total5,
            'forma_pago' => 'efectivo',
        ]);

        $this->command->info('✅ Registro 5 creado: REP-' . str_pad($reparacion5->id, 5, '0', STR_PAD_LEFT));

        // Limpiar caché del dashboard para que se reflejen los cambios inmediatamente
        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.reparaciones_por_mes');

        $this->command->info('');
        $this->command->info('✨ Base de datos limpiada y poblada con 5 registros completos:');
        $this->command->info('   1. Reparación completa (sin impuesto) - ' . $reparacion1->codigo_reparacion);
        $this->command->info('   2. Mantenimiento completo (con impuesto y NCF) - ' . $reparacion2->codigo_reparacion);
        $this->command->info('   3. Garantía (relacionada con REP-00001) - ' . $reparacion3->codigo_reparacion);
        $this->command->info('   4. Mantenimiento con NCF - ' . $reparacion4->codigo_reparacion);
        $this->command->info('   5. GPU con NCF - ' . $reparacion5->codigo_reparacion);
        $this->command->info('');
        $this->command->info('📊 Resumen:');
        $this->command->info('   - Clientes: ' . Cliente::count());
        $this->command->info('   - Equipos: ' . Equipo::count() . ' (con fotos)');
        $this->command->info('   - Reparaciones: ' . Reparacion::count() . ' (2 reparaciones, 2 mantenimientos, 1 garantía)');
        $this->command->info('   - Estados: ' . EstadoReparacion::count());
        $this->command->info('   - Piezas: ' . Pieza::count());
        $this->command->info('   - Notas: ' . NotaReparacion::count());
        $this->command->info('   - Facturas: ' . Factura::count() . ' (1 sin impuesto, 3 con impuesto y NCF)');
        $this->command->info('');
        $this->command->info('🔄 Caché del dashboard limpiado - Los cambios se reflejarán inmediatamente');
    }
}

