<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;

// Redirección de la raíz al login (PRIMERA PÁGINA)
Route::get('/', function () {
    return redirect()->route('login');
});

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.post')
    ->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Módulos
    Route::resource('clientes', \App\Http\Controllers\Web\ClienteController::class);
    Route::resource('equipos', \App\Http\Controllers\Web\EquipoController::class);
    Route::get('equipos/{id}/imprimir-qr', [\App\Http\Controllers\Web\EquipoController::class, 'imprimirQR'])->name('equipos.imprimir-qr');
    Route::resource('reparaciones', \App\Http\Controllers\Web\ReparacionController::class);
    Route::resource('facturas', \App\Http\Controllers\Web\FacturaController::class);
    Route::get('facturas/{id}/imprimir', [\App\Http\Controllers\Web\FacturaController::class, 'imprimir'])->name('facturas.imprimir');
    Route::get('/contabilidad', [\App\Http\Controllers\Web\ContabilidadController::class, 'index'])->name('contabilidad.index');
    Route::resource('usuarios', \App\Http\Controllers\Web\UsuarioController::class)->middleware('role:administrador');
});
