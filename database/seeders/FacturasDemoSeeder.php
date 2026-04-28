<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Equipo;
use App\Models\Reparacion;
use App\Models\Factura;
use App\Models\User;
use App\Models\EstadoReparacion;
use App\Models\EquipoFoto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FacturasDemoSeeder extends Seeder
{
    /**
     * Crea una imagen de prueba para un equipo
     */
    private function crearImagenPrueba($equipoId, $nombreArchivo, $texto = null, $colorFondo = null)
    {
        // Crear imagen de 800x600
        $ancho = 800;
        $alto = 600;
        $imagen = imagecreatetruecolor($ancho, $alto);
        
        // Colores aleatorios si no se especifican
        if (!$colorFondo) {
            $r = rand(200, 255);
            $g = rand(200, 255);
            $b = rand(200, 255);
        } else {
            list($r, $g, $b) = $colorFondo;
        }
        
        $fondo = imagecolorallocate($imagen, $r, $g, $b);
        imagefill($imagen, 0, 0, $fondo);
        
        // Color del texto
        $colorTexto = imagecolorallocate($imagen, 50, 50, 50);
        
        // Agregar texto si se proporciona
        if ($texto) {
            $fontSize = 5;
            $x = ($ancho - strlen($texto) * imagefontwidth($fontSize)) / 2;
            $y = ($alto - imagefontheight($fontSize)) / 2;
            imagestring($imagen, $fontSize, $x, $y, $texto, $colorTexto);
        }
        
        // Guardar imagen
        $directorio = storage_path('app/public/equipos/' . $equipoId);
        if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
        }
        
        $rutaCompleta = $directorio . '/' . $nombreArchivo;
        imagejpeg($imagen, $rutaCompleta, 85);
        imagedestroy($imagen);
        
        return 'equipos/' . $equipoId . '/' . $nombreArchivo;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener o crear clientes
        $cliente1 = Cliente::firstOrCreate(
            ['telefono' => '8095551001'],
            [
                'nombre' => 'Juan Pérez',
                'cedula_rnc' => '001-1234567-8',
                'email' => 'juan.perez@email.com',
                'direccion' => 'Calle Principal #123, Santo Domingo',
            ]
        );

        $cliente2 = Cliente::firstOrCreate(
            ['telefono' => '8095551002'],
            [
                'nombre' => 'María Rodríguez',
                'cedula_rnc' => '001-2345678-9',
                'email' => 'maria.rodriguez@email.com',
                'direccion' => 'Av. Independencia #456, Santiago',
            ]
        );

        $cliente3 = Cliente::firstOrCreate(
            ['telefono' => '8095551003'],
            [
                'nombre' => 'Carlos Martínez',
                'cedula_rnc' => '001-3456789-0',
                'email' => 'carlos.martinez@email.com',
                'direccion' => 'Calle Duarte #789, La Vega',
            ]
        );

        $cliente4 = Cliente::firstOrCreate(
            ['telefono' => '8095551004'],
            [
                'nombre' => 'Ana López',
                'cedula_rnc' => '001-4567890-1',
                'email' => 'ana.lopez@email.com',
                'direccion' => 'Av. 27 de Febrero #321, Santo Domingo',
            ]
        );

        $cliente5 = Cliente::firstOrCreate(
            ['telefono' => '8095551005'],
            [
                'nombre' => 'Pedro Sánchez',
                'cedula_rnc' => '001-5678901-2',
                'email' => 'pedro.sanchez@email.com',
                'direccion' => 'Calle Mella #654, San Pedro de Macorís',
            ]
        );

        $cliente6 = Cliente::firstOrCreate(
            ['telefono' => '8095551006'],
            [
                'nombre' => 'Laura Fernández',
                'cedula_rnc' => '001-6789012-3',
                'email' => 'laura.fernandez@email.com',
                'direccion' => 'Av. Winston Churchill #987, Santo Domingo',
            ]
        );

        // Obtener usuarios para recepcionista y técnico
        $recepcionista = User::first();
        $tecnico = User::whereHas('roles', function($q) {
            $q->where('slug', 'tecnico');
        })->first() ?? User::first();

        // Obtener último ID de factura para generar números secuenciales
        $ultimoIdFactura = Factura::max('id') ?? 0;

        // ========== FACTURA 1: REPARACIÓN ==========
        $equipo1 = Equipo::create([
            'cliente_id' => $cliente1->id,
            'tipo' => 'Laptop',
            'marca' => 'HP',
            'modelo' => 'Pavilion 15',
            'numero_serie' => 'HP-LAP-001',
            'descripcion_problema' => 'No enciende, posible problema de placa madre',
        ]);

        $reparacion1 = Reparacion::create([
            'equipo_id' => $equipo1->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'tipo_servicio' => 'reparacion',
            'estado' => 'Finalizado',
            'fecha_ingreso' => Carbon::now()->subDays(10),
            'fecha_finalizacion' => Carbon::now()->subDays(2),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 150.00,
            'costo_mano_obra' => 100.00,
            'total_estimado' => 300.00,
            'precio_cotizado' => 300.00,
            'fecha_cotizacion' => Carbon::now()->subDays(8),
            'cliente_aprobado' => true,
            'fecha_aprobacion' => Carbon::now()->subDays(7),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion1->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido y registrado en el sistema',
            'usuario_id' => $recepcionista->id,
        ]);

        // Agregar fotos al equipo 1
        $ruta1 = $this->crearImagenPrueba($equipo1->id, 'foto-1.jpg', 'HP Pavilion 15 - Vista Frontal', [240, 240, 240]);
        EquipoFoto::create([
            'equipo_id' => $equipo1->id,
            'ruta' => $ruta1,
            'nombre_original' => 'foto-frontal.jpg',
            'orden' => 0,
        ]);
        $ruta2 = $this->crearImagenPrueba($equipo1->id, 'foto-2.jpg', 'HP Pavilion 15 - Vista Lateral', [230, 230, 250]);
        EquipoFoto::create([
            'equipo_id' => $equipo1->id,
            'ruta' => $ruta2,
            'nombre_original' => 'foto-lateral.jpg',
            'orden' => 1,
        ]);

        $ultimoIdFactura++;
        Factura::create([
            'reparacion_id' => $reparacion1->id,
            'equipo_id' => $equipo1->id,
            'cliente_id' => $cliente1->id,
            'numero_factura' => 'FAC-' . str_pad($ultimoIdFactura, 6, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::now()->subDays(2),
            'subtotal' => 300.00,
            'impuestos' => 54.00,
            'total' => 354.00,
            'forma_pago' => 'efectivo',
        ]);

        // ========== FACTURA 2: REPARACIÓN ==========
        $equipo2 = Equipo::create([
            'cliente_id' => $cliente2->id,
            'tipo' => 'PC de Escritorio',
            'marca' => 'Dell',
            'modelo' => 'OptiPlex 7090',
            'numero_serie' => 'DELL-DESK-002',
            'descripcion_problema' => 'Pantalla azul, error de sistema',
        ]);

        $reparacion2 = Reparacion::create([
            'equipo_id' => $equipo2->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'tipo_servicio' => 'reparacion',
            'estado' => 'Finalizado',
            'fecha_ingreso' => Carbon::now()->subDays(8),
            'fecha_finalizacion' => Carbon::now()->subDays(1),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 80.00,
            'costo_mano_obra' => 120.00,
            'total_estimado' => 250.00,
            'precio_cotizado' => 250.00,
            'fecha_cotizacion' => Carbon::now()->subDays(6),
            'cliente_aprobado' => true,
            'fecha_aprobacion' => Carbon::now()->subDays(5),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion2->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido y registrado en el sistema',
            'usuario_id' => $recepcionista->id,
        ]);

        // Agregar fotos al equipo 2
        $ruta1 = $this->crearImagenPrueba($equipo2->id, 'foto-1.jpg', 'Dell OptiPlex 7090', [250, 240, 230]);
        EquipoFoto::create([
            'equipo_id' => $equipo2->id,
            'ruta' => $ruta1,
            'nombre_original' => 'foto-equipo.jpg',
            'orden' => 0,
        ]);

        $ultimoIdFactura++;
        Factura::create([
            'reparacion_id' => $reparacion2->id,
            'equipo_id' => $equipo2->id,
            'cliente_id' => $cliente2->id,
            'numero_factura' => 'FAC-' . str_pad($ultimoIdFactura, 6, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::now()->subDays(1),
            'subtotal' => 250.00,
            'impuestos' => 45.00,
            'total' => 295.00,
            'forma_pago' => 'transferencia',
        ]);

        // ========== FACTURA 3: GPU ==========
        $equipo3 = Equipo::create([
            'cliente_id' => $cliente3->id,
            'tipo' => 'Tarjeta Gráfica (GPU)',
            'marca' => 'NVIDIA',
            'modelo' => 'RTX 3060',
            'numero_serie' => 'NVIDIA-GPU-003',
            'descripcion_problema' => 'Sobrecalentamiento, necesita limpieza y cambio de pasta térmica',
        ]);

        $reparacion3 = Reparacion::create([
            'equipo_id' => $equipo3->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'tipo_servicio' => 'reparacion',
            'estado' => 'Finalizado',
            'fecha_ingreso' => Carbon::now()->subDays(7),
            'fecha_finalizacion' => Carbon::now()->subDays(3),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 25.00,
            'costo_mano_obra' => 75.00,
            'total_estimado' => 150.00,
            'precio_cotizado' => 150.00,
            'fecha_cotizacion' => Carbon::now()->subDays(5),
            'cliente_aprobado' => true,
            'fecha_aprobacion' => Carbon::now()->subDays(4),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion3->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido y registrado en el sistema',
            'usuario_id' => $recepcionista->id,
        ]);

        // Agregar fotos al equipo 3 (GPU)
        $ruta1 = $this->crearImagenPrueba($equipo3->id, 'foto-1.jpg', 'NVIDIA RTX 3060 - Vista Superior', [200, 200, 220]);
        EquipoFoto::create([
            'equipo_id' => $equipo3->id,
            'ruta' => $ruta1,
            'nombre_original' => 'gpu-vista-superior.jpg',
            'orden' => 0,
        ]);
        $ruta2 = $this->crearImagenPrueba($equipo3->id, 'foto-2.jpg', 'NVIDIA RTX 3060 - Ventiladores', [220, 200, 200]);
        EquipoFoto::create([
            'equipo_id' => $equipo3->id,
            'ruta' => $ruta2,
            'nombre_original' => 'gpu-ventiladores.jpg',
            'orden' => 1,
        ]);
        $ruta3 = $this->crearImagenPrueba($equipo3->id, 'foto-3.jpg', 'NVIDIA RTX 3060 - Conectores', [200, 220, 200]);
        EquipoFoto::create([
            'equipo_id' => $equipo3->id,
            'ruta' => $ruta3,
            'nombre_original' => 'gpu-conectores.jpg',
            'orden' => 2,
        ]);

        $ultimoIdFactura++;
        Factura::create([
            'reparacion_id' => $reparacion3->id,
            'equipo_id' => $equipo3->id,
            'cliente_id' => $cliente3->id,
            'numero_factura' => 'FAC-' . str_pad($ultimoIdFactura, 6, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::now()->subDays(3),
            'subtotal' => 150.00,
            'impuestos' => 27.00,
            'total' => 177.00,
            'forma_pago' => 'efectivo',
        ]);

        // ========== FACTURA 4: GPU ==========
        $equipo4 = Equipo::create([
            'cliente_id' => $cliente4->id,
            'tipo' => 'Tarjeta Gráfica (GPU)',
            'marca' => 'AMD',
            'modelo' => 'RX 6700 XT',
            'numero_serie' => 'AMD-GPU-004',
            'descripcion_problema' => 'Artefactos en pantalla, posible problema de memoria',
        ]);

        $reparacion4 = Reparacion::create([
            'equipo_id' => $equipo4->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'tipo_servicio' => 'reparacion',
            'estado' => 'Finalizado',
            'fecha_ingreso' => Carbon::now()->subDays(6),
            'fecha_finalizacion' => Carbon::now()->subDays(2),
            'costo_diagnostico' => 50.00,
            'costo_piezas' => 200.00,
            'costo_mano_obra' => 100.00,
            'total_estimado' => 350.00,
            'precio_cotizado' => 350.00,
            'fecha_cotizacion' => Carbon::now()->subDays(4),
            'cliente_aprobado' => true,
            'fecha_aprobacion' => Carbon::now()->subDays(3),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion4->id,
            'estado' => 'Recibido',
            'comentario' => 'Equipo recibido y registrado en el sistema',
            'usuario_id' => $recepcionista->id,
        ]);

        // Agregar fotos al equipo 4 (GPU)
        $ruta1 = $this->crearImagenPrueba($equipo4->id, 'foto-1.jpg', 'AMD RX 6700 XT', [220, 220, 200]);
        EquipoFoto::create([
            'equipo_id' => $equipo4->id,
            'ruta' => $ruta1,
            'nombre_original' => 'amd-gpu-1.jpg',
            'orden' => 0,
        ]);
        $ruta2 = $this->crearImagenPrueba($equipo4->id, 'foto-2.jpg', 'AMD RX 6700 XT - Detalle', [200, 220, 220]);
        EquipoFoto::create([
            'equipo_id' => $equipo4->id,
            'ruta' => $ruta2,
            'nombre_original' => 'amd-gpu-2.jpg',
            'orden' => 1,
        ]);

        $ultimoIdFactura++;
        Factura::create([
            'reparacion_id' => $reparacion4->id,
            'equipo_id' => $equipo4->id,
            'cliente_id' => $cliente4->id,
            'numero_factura' => 'FAC-' . str_pad($ultimoIdFactura, 6, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::now()->subDays(2),
            'subtotal' => 350.00,
            'impuestos' => 63.00,
            'total' => 413.00,
            'forma_pago' => 'tarjeta',
        ]);

        // ========== FACTURA 5: MANTENIMIENTO ==========
        $equipo5 = Equipo::create([
            'cliente_id' => $cliente5->id,
            'tipo' => 'Laptop',
            'marca' => 'Lenovo',
            'modelo' => 'ThinkPad E14',
            'numero_serie' => 'LEN-LAP-005',
            'descripcion_problema' => 'Limpieza general y mantenimiento preventivo',
        ]);

        $reparacion5 = Reparacion::create([
            'equipo_id' => $equipo5->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'tipo_servicio' => 'mantenimiento',
            'estado' => 'En Proceso',
            'fecha_ingreso' => Carbon::now()->subDays(5),
            'costo_diagnostico' => 0,
            'costo_piezas' => 0,
            'costo_mano_obra' => 0,
            'total_estimado' => 0,
            'precio_cotizado' => 80.00,
            'fecha_cotizacion' => Carbon::now()->subDays(5),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion5->id,
            'estado' => 'Recibido',
            'comentario' => 'Mantenimiento recibido y registrado en el sistema',
            'usuario_id' => $recepcionista->id,
        ]);

        // Agregar fotos al equipo 5
        $ruta1 = $this->crearImagenPrueba($equipo5->id, 'foto-1.jpg', 'Lenovo ThinkPad E14', [240, 250, 240]);
        EquipoFoto::create([
            'equipo_id' => $equipo5->id,
            'ruta' => $ruta1,
            'nombre_original' => 'thinkpad-cerrado.jpg',
            'orden' => 0,
        ]);
        $ruta2 = $this->crearImagenPrueba($equipo5->id, 'foto-2.jpg', 'Lenovo ThinkPad E14 - Abierto', [250, 250, 240]);
        EquipoFoto::create([
            'equipo_id' => $equipo5->id,
            'ruta' => $ruta2,
            'nombre_original' => 'thinkpad-abierto.jpg',
            'orden' => 1,
        ]);

        $ultimoIdFactura++;
        Factura::create([
            'reparacion_id' => $reparacion5->id,
            'equipo_id' => $equipo5->id,
            'cliente_id' => $cliente5->id,
            'numero_factura' => 'FAC-' . str_pad($ultimoIdFactura, 6, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::now()->subDays(5),
            'subtotal' => 80.00,
            'impuestos' => 0,
            'total' => 80.00,
            'forma_pago' => 'efectivo',
        ]);

        // ========== FACTURA 6: MANTENIMIENTO ==========
        $equipo6 = Equipo::create([
            'cliente_id' => $cliente6->id,
            'tipo' => 'PC de Escritorio',
            'marca' => 'ASUS',
            'modelo' => 'ROG Strix',
            'numero_serie' => 'ASUS-DESK-006',
            'descripcion_problema' => 'Limpieza interna, cambio de pasta térmica y optimización',
        ]);

        $reparacion6 = Reparacion::create([
            'equipo_id' => $equipo6->id,
            'tecnico_id' => $tecnico->id,
            'recepcionista_id' => $recepcionista->id,
            'tipo_servicio' => 'mantenimiento',
            'estado' => 'En Proceso',
            'fecha_ingreso' => Carbon::now()->subDays(4),
            'costo_diagnostico' => 0,
            'costo_piezas' => 0,
            'costo_mano_obra' => 0,
            'total_estimado' => 0,
            'precio_cotizado' => 120.00,
            'fecha_cotizacion' => Carbon::now()->subDays(4),
        ]);

        EstadoReparacion::create([
            'reparacion_id' => $reparacion6->id,
            'estado' => 'Recibido',
            'comentario' => 'Mantenimiento recibido y registrado en el sistema',
            'usuario_id' => $recepcionista->id,
        ]);

        // Agregar fotos al equipo 6
        $ruta1 = $this->crearImagenPrueba($equipo6->id, 'foto-1.jpg', 'ASUS ROG Strix', [220, 200, 220]);
        EquipoFoto::create([
            'equipo_id' => $equipo6->id,
            'ruta' => $ruta1,
            'nombre_original' => 'rog-strix-frontal.jpg',
            'orden' => 0,
        ]);
        $ruta2 = $this->crearImagenPrueba($equipo6->id, 'foto-2.jpg', 'ASUS ROG Strix - Interior', [200, 220, 220]);
        EquipoFoto::create([
            'equipo_id' => $equipo6->id,
            'ruta' => $ruta2,
            'nombre_original' => 'rog-strix-interior.jpg',
            'orden' => 1,
        ]);
        $ruta3 = $this->crearImagenPrueba($equipo6->id, 'foto-3.jpg', 'ASUS ROG Strix - Detalle RGB', [240, 200, 200]);
        EquipoFoto::create([
            'equipo_id' => $equipo6->id,
            'ruta' => $ruta3,
            'nombre_original' => 'rog-strix-rgb.jpg',
            'orden' => 2,
        ]);

        $ultimoIdFactura++;
        Factura::create([
            'reparacion_id' => $reparacion6->id,
            'equipo_id' => $equipo6->id,
            'cliente_id' => $cliente6->id,
            'numero_factura' => 'FAC-' . str_pad($ultimoIdFactura, 6, '0', STR_PAD_LEFT),
            'fecha_emision' => Carbon::now()->subDays(4),
            'subtotal' => 120.00,
            'impuestos' => 0,
            'total' => 120.00,
            'forma_pago' => 'transferencia',
        ]);

        $this->command->info('✅ Se crearon 6 facturas de prueba:');
        $this->command->info('   - 2 Facturas de Reparación');
        $this->command->info('   - 2 Facturas de GPU');
        $this->command->info('   - 2 Facturas de Mantenimiento');
    }
}
