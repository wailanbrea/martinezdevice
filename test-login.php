<?php
// Script de prueba para diagnosticar el login
// Ejecutar: php test-login.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test de Login ===\n\n";

// 1. Verificar conexión a BD
echo "1. Probando conexión a base de datos...\n";
try {
    $user = \App\Models\User::where('email', 'admin@martinezservice.com')->first();
    if ($user) {
        echo "   ✓ Usuario encontrado: {$user->email}\n";
        echo "   ✓ ID: {$user->id}\n";
        echo "   ✓ Nombre: {$user->name}\n";
    } else {
        echo "   ✗ Usuario NO encontrado\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

// 2. Verificar autenticación
echo "\n2. Probando autenticación...\n";
try {
    $credentials = [
        'email' => 'admin@martinezservice.com',
        'password' => 'password'
    ];
    
    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        echo "   ✓ Autenticación EXITOSA\n";
        $user = \Illuminate\Support\Facades\Auth::user();
        echo "   ✓ Usuario autenticado: {$user->email}\n";
        
        // 3. Probar creación de sesión
        echo "\n3. Probando creación de sesión...\n";
        $session = app('session');
        $session->put('test_key', 'test_value');
        $session->save();
        echo "   ✓ Sesión guardada correctamente\n";
        
    } else {
        echo "   ✗ Autenticación FALLIDA\n";
        echo "   Las credenciales no coinciden\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error durante autenticación: " . $e->getMessage() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

// 4. Verificar directorios
echo "\n4. Verificando directorios de sesión...\n";
$sessionPath = storage_path('framework/sessions');
if (is_dir($sessionPath)) {
    echo "   ✓ Directorio de sesiones existe\n";
    if (is_writable($sessionPath)) {
        echo "   ✓ Directorio es escribible\n";
    } else {
        echo "   ✗ Directorio NO es escribible\n";
    }
} else {
    echo "   ✗ Directorio de sesiones NO existe\n";
}

// 5. Verificar configuración
echo "\n5. Verificación de configuración:\n";
echo "   SESSION_DRIVER: " . config('session.driver') . "\n";
echo "   CACHE_STORE: " . config('cache.default') . "\n";
echo "   LOG_CHANNEL: " . config('logging.default') . "\n";
echo "   APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "   APP_ENV: " . config('app.env') . "\n";

echo "\n=== Fin del Test ===\n";

