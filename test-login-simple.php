<?php
// Test simple de login
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Test Simple de Login ===\n\n";

// 1. Obtener usuario
$user = \App\Models\User::where('email', 'admin@martinezservice.com')->first();
echo "1. Usuario: {$user->email}\n";
echo "   ID: {$user->id}\n";
echo "   Password hash: " . substr($user->password, 0, 30) . "...\n\n";

// 2. Verificar password directamente con PHP
$passwordCheck = password_verify('password', $user->password);
echo "2. password_verify (PHP nativo): " . ($passwordCheck ? "✓ VÁLIDO" : "✗ INVÁLIDO") . "\n\n";

// 3. Verificar con Hash facade
try {
    $hashCheck = \Hash::check('password', $user->password);
    echo "3. Hash::check (Laravel): " . ($hashCheck ? "✓ VÁLIDO" : "✗ INVÁLIDO") . "\n\n";
} catch (\Exception $e) {
    echo "3. Hash::check ERROR: " . $e->getMessage() . "\n\n";
}

// 4. Intentar login manual
echo "4. Intentando login manual...\n";
try {
    // Login directo sin Auth::attempt
    \Auth::login($user);
    echo "   ✓ Login manual EXITOSO\n";
    echo "   Usuario autenticado: " . \Auth::user()->email . "\n\n";
    \Auth::logout();
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n\n";
}

// 5. Ver configuración de hashing
echo "5. Configuración:\n";
echo "   driver: " . config('hashing.driver') . "\n";
echo "   rehash_on_login: " . (config('hashing.rehash_on_login') ? 'true' : 'false') . "\n";
echo "   bcrypt.verify: " . (config('hashing.bcrypt.verify') ? 'true' : 'false') . "\n";

echo "\n=== Fin ===\n";

