<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\EquipoController;
use App\Http\Controllers\Api\ReparacionController;
use App\Http\Controllers\Api\FacturaController;
use App\Http\Controllers\Api\ContabilidadController;
use App\Http\Controllers\Api\PublicController;

// Endpoint de salud
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Martínez Service API is running'
    ]);
});

// Endpoint público (sin autenticación)
Route::get('/public/status/{codigo_unico}', [PublicController::class, 'status'])
    ->name('api.public.status');

// Autenticación
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Rutas protegidas con Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Usuario autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Clientes (RESTful) - Con prefijo 'api.' en los nombres
    Route::apiResource('clientes', ClienteController::class)->names([
        'index' => 'api.clientes.index',
        'show' => 'api.clientes.show',
        'store' => 'api.clientes.store',
        'update' => 'api.clientes.update',
        'destroy' => 'api.clientes.destroy',
    ]);

    // Equipos (RESTful) - Con prefijo 'api.' en los nombres
    Route::apiResource('equipos', EquipoController::class)->names([
        'index' => 'api.equipos.index',
        'show' => 'api.equipos.show',
        'store' => 'api.equipos.store',
        'update' => 'api.equipos.update',
        'destroy' => 'api.equipos.destroy',
    ]);
    Route::post('/equipos/{equipo}/generar-qr', [EquipoController::class, 'generarQR'])
        ->name('api.equipos.generar-qr');

    // Reparaciones (RESTful) - Con prefijo 'api.' en los nombres
    Route::apiResource('reparaciones', ReparacionController::class)->names([
        'index' => 'api.reparaciones.index',
        'show' => 'api.reparaciones.show',
        'store' => 'api.reparaciones.store',
        'update' => 'api.reparaciones.update',
        'destroy' => 'api.reparaciones.destroy',
    ]);

    // Facturas (RESTful) - Con prefijo 'api.' en los nombres
    Route::apiResource('facturas', FacturaController::class)->names([
        'index' => 'api.facturas.index',
        'show' => 'api.facturas.show',
        'store' => 'api.facturas.store',
        'update' => 'api.facturas.update',
        'destroy' => 'api.facturas.destroy',
    ]);
    Route::get('/facturas/{factura}/descargar', [FacturaController::class, 'descargar'])
        ->name('api.facturas.descargar');
    Route::get('/facturas/{factura}/imprimir', [FacturaController::class, 'imprimir'])
        ->name('api.facturas.imprimir');

    // Contabilidad y Reportes
    Route::prefix('reportes')->group(function () {
        Route::get('/ingresos', [ContabilidadController::class, 'ingresos'])
            ->name('api.reportes.ingresos');
        Route::get('/ingresos/diarios', [ContabilidadController::class, 'ingresosDiarios'])
            ->name('api.reportes.ingresos.diarios');
        Route::get('/ingresos/semanales', [ContabilidadController::class, 'ingresosSemanales'])
            ->name('api.reportes.ingresos.semanales');
        Route::get('/ingresos/mensuales', [ContabilidadController::class, 'ingresosMensuales'])
            ->name('api.reportes.ingresos.mensuales');
        Route::get('/ingresos/exportar/csv', [ContabilidadController::class, 'exportarCSV'])
            ->name('api.reportes.ingresos.csv');
        Route::get('/ingresos/exportar/pdf', [ContabilidadController::class, 'exportarPDF'])
            ->name('api.reportes.ingresos.pdf');
    });
});
