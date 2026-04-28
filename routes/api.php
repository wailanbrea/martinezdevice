<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\EquipoController;
use App\Http\Controllers\Api\ReparacionController;
use App\Http\Controllers\Api\FacturaController;
use App\Http\Controllers\Api\PublicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::get('/public/status/{codigo}', [PublicController::class, 'consultarEstado']);
Route::post('/public/aprobar/{codigo}', [PublicController::class, 'aprobarCotizacion']);
Route::post('/public/rechazar/{codigo}', [PublicController::class, 'rechazarCotizacion']);

// Ruta pública para buscar reparación por código (usada en formulario de garantía)
Route::get('/reparaciones/buscar/{codigo}', [ReparacionController::class, 'buscarPorCodigo'])->name('api.reparaciones.buscar');

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Clientes
    Route::apiResource('clientes', ClienteController::class)->names([
        'index' => 'api.clientes.index',
        'store' => 'api.clientes.store',
        'show' => 'api.clientes.show',
        'update' => 'api.clientes.update',
        'destroy' => 'api.clientes.destroy',
    ]);

    // Equipos
    Route::apiResource('equipos', EquipoController::class)->names([
        'index' => 'api.equipos.index',
        'store' => 'api.equipos.store',
        'show' => 'api.equipos.show',
        'update' => 'api.equipos.update',
        'destroy' => 'api.equipos.destroy',
    ]);

    // Reparaciones
    Route::get('/reparaciones/estadisticas', [ReparacionController::class, 'estadisticas'])->name('api.reparaciones.estadisticas');
    Route::post('/reparaciones/{reparacion}/notas', [ReparacionController::class, 'addNota'])->name('api.reparaciones.notas');
    Route::apiResource('reparaciones', ReparacionController::class)->names([
        'index' => 'api.reparaciones.index',
        'store' => 'api.reparaciones.store',
        'show' => 'api.reparaciones.show',
        'update' => 'api.reparaciones.update',
        'destroy' => 'api.reparaciones.destroy',
    ]);

    // Facturas
    Route::apiResource('facturas', FacturaController::class)->names([
        'index' => 'api.facturas.index',
        'store' => 'api.facturas.store',
        'show' => 'api.facturas.show',
        'update' => 'api.facturas.update',
        'destroy' => 'api.facturas.destroy',
    ]);
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'descargarPDF'])->name('api.facturas.pdf');
});

