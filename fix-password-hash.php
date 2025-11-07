<?php
// Script para recrear el usuario administrador con hash simple
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Recreando Usuario Administrador ===\n\n";

// Eliminar usuario anterior
\DB::table('users')->where('email', 'admin@martinezservice.com')->delete();
echo "✓ Usuario anterior eliminado\n";

// Crear hash de contraseña usando password_hash nativo
$password = password_hash('password', PASSWORD_DEFAULT);
echo "✓ Hash creado: " . substr($password, 0, 30) . "...\n";

// Insertar usuario directamente
\DB::table('users')->insert([
    'name' => 'Administrador',
    'email' => 'admin@martinezservice.com',
    'password' => $password,
    'created_at' => now(),
    'updated_at' => now(),
]);

echo "✓ Usuario creado exitosamente\n\n";

// Verificar
$user = \DB::table('users')->where('email', 'admin@martinezservice.com')->first();
echo "Usuario en BD:\n";
echo "  ID: {$user->id}\n";
echo "  Nombre: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Password hash: " . substr($user->password, 0, 30) . "...\n\n";

// Probar verificación
$isValid = password_verify('password', $user->password);
echo "Verificación de contraseña: " . ($isValid ? "✓ VÁLIDA" : "✗ INVÁLIDA") . "\n\n";

echo "=== Completado ===\n";

