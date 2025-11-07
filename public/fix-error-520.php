<?php
/**
 * Script Temporal para Solucionar Error 520
 * 
 * IMPORTANTE: 
 * 1. Cambia la clave de seguridad abajo
 * 2. Accede a: https://demomartinez.bsolutions.dev/fix-error-520.php?key=TU_CLAVE
 * 3. BORRA este archivo después de usarlo
 */

// ⚠️ CAMBIA ESTA CLAVE POR UNA ALEATORIA
$clave_seguridad = 'martinez-service-fix-520-' . date('Ymd');

// Verificar clave de seguridad
if (!isset($_GET['key']) || $_GET['key'] !== $clave_seguridad) {
    http_response_code(403);
    die('❌ Acceso denegado. Usa la clave correcta.');
}

// Configuración
$project_dir = '/var/www/martinezdevice';

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fix Error 520 - Martínez Service</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            padding: 30px;
        }
        h1 {
            color: #667eea;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .step {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .step h3 {
            margin-top: 0;
            color: #667eea;
        }
        pre {
            background: #2d3748;
            color: #a0aec0;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            font-size: 14px;
            line-height: 1.5;
        }
        .success {
            color: #48bb78;
            font-weight: bold;
        }
        .error {
            color: #f56565;
            font-weight: bold;
        }
        .warning {
            color: #ed8936;
            font-weight: bold;
        }
        .alert {
            background: #fff5f5;
            border: 2px solid #fc8181;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }
        .alert h3 {
            margin-top: 0;
            color: #c53030;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Solución Error 520 - Martínez Service</h1>
        
        <div class="alert">
            <h3>⚠️ IMPORTANTE</h3>
            <p><strong>Borra este archivo después de usarlo por seguridad:</strong></p>
            <code>rm /var/www/martinezdevice/public/fix-error-520.php</code>
        </div>

        <?php
        // Verificar que estamos en el directorio correcto
        if (!is_dir($project_dir)) {
            echo '<div class="error">❌ Error: No se encontró el directorio del proyecto</div>';
            exit;
        }

        // Cambiar al directorio del proyecto
        chdir($project_dir);

        echo '<div class="step">';
        echo '<h3>📋 Información del Sistema</h3>';
        echo '<pre>';
        echo 'Directorio: ' . getcwd() . "\n";
        echo 'PHP Version: ' . PHP_VERSION . "\n";
        echo 'Usuario: ' . get_current_user() . "\n";
        echo '</pre>';
        echo '</div>';

        // Paso 1: Actualizar desde GitHub
        echo '<div class="step">';
        echo '<h3>1️⃣ Actualizando desde GitHub</h3>';
        echo '<pre>';
        $output = shell_exec('git pull origin main 2>&1');
        echo htmlspecialchars($output);
        echo '</pre>';
        echo '</div>';

        // Paso 2: Limpiar sesiones
        echo '<div class="step">';
        echo '<h3>2️⃣ Limpiando sesiones antiguas</h3>';
        echo '<pre>';
        $sessions_deleted = 0;
        $sessions_dir = $project_dir . '/storage/framework/sessions';
        if (is_dir($sessions_dir)) {
            $files = glob($sessions_dir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $sessions_deleted++;
                }
            }
            echo "✅ $sessions_deleted sesiones eliminadas\n";
        } else {
            echo "⚠️ Directorio de sesiones no encontrado\n";
        }
        echo '</pre>';
        echo '</div>';

        // Paso 3: Configurar permisos
        echo '<div class="step">';
        echo '<h3>3️⃣ Configurando permisos</h3>';
        echo '<pre>';
        exec('chown -R www-data:www-data storage bootstrap/cache 2>&1', $output1, $return1);
        exec('chmod -R 775 storage bootstrap/cache 2>&1', $output2, $return2);
        if ($return1 === 0 && $return2 === 0) {
            echo "✅ Permisos configurados correctamente\n";
        } else {
            echo "⚠️ Algunos permisos pueden no haberse configurado correctamente\n";
            if (!empty($output1)) echo implode("\n", $output1) . "\n";
            if (!empty($output2)) echo implode("\n", $output2) . "\n";
        }
        echo '</pre>';
        echo '</div>';

        // Paso 4: Limpiar cachés de Laravel
        echo '<div class="step">';
        echo '<h3>4️⃣ Limpiando cachés de Laravel</h3>';
        echo '<pre>';
        $commands = [
            'config:clear' => 'Configuración',
            'cache:clear' => 'Cache',
            'route:clear' => 'Rutas',
            'view:clear' => 'Vistas',
        ];
        foreach ($commands as $cmd => $name) {
            exec("php artisan $cmd 2>&1", $output, $return);
            if ($return === 0) {
                echo "✅ $name limpiada\n";
            } else {
                echo "⚠️ Error al limpiar $name\n";
            }
            unset($output, $return);
        }
        echo '</pre>';
        echo '</div>';

        // Paso 5: Cachear configuración
        echo '<div class="step">';
        echo '<h3>5️⃣ Cacheando configuración</h3>';
        echo '<pre>';
        $cache_commands = [
            'config:cache' => 'Configuración',
            'route:cache' => 'Rutas',
            'view:cache' => 'Vistas',
        ];
        foreach ($cache_commands as $cmd => $name) {
            exec("php artisan $cmd 2>&1", $output, $return);
            if ($return === 0) {
                echo "✅ $name cacheada\n";
            } else {
                echo "⚠️ Error al cachear $name\n";
            }
            unset($output, $return);
        }
        echo '</pre>';
        echo '</div>';

        // Paso 6: Reiniciar servicios
        echo '<div class="step">';
        echo '<h3>6️⃣ Reiniciando servicios</h3>';
        echo '<pre>';
        
        // Reiniciar PHP-FPM
        exec('systemctl restart php8.3-fpm 2>&1', $output, $return);
        if ($return === 0) {
            echo "✅ PHP-FPM reiniciado\n";
        } else {
            echo "⚠️ No se pudo reiniciar PHP-FPM (puede requerir permisos sudo)\n";
        }
        unset($output, $return);
        
        // Recargar Nginx
        exec('systemctl reload nginx 2>&1', $output, $return);
        if ($return === 0) {
            echo "✅ Nginx recargado\n";
        } else {
            echo "⚠️ No se pudo recargar Nginx (puede requerir permisos sudo)\n";
        }
        echo '</pre>';
        echo '</div>';

        // Resultado final
        echo '<div class="step" style="border-left-color: #48bb78; background: #f0fff4;">';
        echo '<h3 class="success">✅ Proceso Completado</h3>';
        echo '<p>Ahora prueba el login en:</p>';
        echo '<p><strong><a href="https://demomartinez.bsolutions.dev/login" target="_blank">https://demomartinez.bsolutions.dev/login</a></strong></p>';
        echo '<p><strong>Credenciales:</strong></p>';
        echo '<ul>';
        echo '<li>Email: admin@martinezservice.com</li>';
        echo '<li>Password: password</li>';
        echo '</ul>';
        echo '</div>';

        ?>

        <div class="alert">
            <h3>🗑️ BORRA ESTE ARCHIVO AHORA</h3>
            <p>Por seguridad, ejecuta este comando en el servidor:</p>
            <pre>rm /var/www/martinezdevice/public/fix-error-520.php</pre>
            <p>O bórralo manualmente desde tu gestor de archivos/FTP</p>
        </div>
    </div>
</body>
</html>

