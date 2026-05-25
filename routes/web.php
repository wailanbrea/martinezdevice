<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ActividadUsuarioController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\ReparacionController;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ContabilidadController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\FacturaConfiguracionController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReparacionesController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\SistemaConfiguracionController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UsuariosController;

// Ruta raiz
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticacion (guest)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.perform');
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
    Route::get('/reset-password', [ResetPassword::class, 'show'])->name('reset-password');
    Route::post('/reset-password', [ResetPassword::class, 'send'])->name('reset.perform');
    Route::get('/change-password', [ChangePassword::class, 'show'])->name('change-password');
    Route::post('/change-password', [ChangePassword::class, 'update'])->name('change.perform');
});

// Rutas protegidas (autenticadas)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/check', [DashboardController::class, 'check'])->name('dashboard.check');

    Route::get('/api/reparaciones/buscar/{codigo}', [ReparacionController::class, 'buscarPorCodigo'])
        ->name('api.reparaciones.buscar.web');

    Route::resource('reparaciones', ReparacionesController::class);
    Route::post('/reparaciones/{id}/notas', [ReparacionesController::class, 'addNota'])->name('reparaciones.notas');
    Route::put('/reparaciones/{reparacion}/historial/{historial}', [ReparacionesController::class, 'updateHistorialComentario'])->name('reparaciones.historial.update');
    Route::delete('/reparaciones/{reparacion}/historial/{historial}', [ReparacionesController::class, 'destroyHistorialComentario'])->name('reparaciones.historial.destroy');
    Route::get('/reparaciones/{reparacion}/whatsapp-entrada', [ReparacionesController::class, 'compartirEntradaWhatsApp'])->name('reparaciones.whatsapp-entrada');
    Route::get('/reparaciones/{reparacion}/whatsapp-estado', [ReparacionesController::class, 'compartirEstadoWhatsApp'])->name('reparaciones.whatsapp-estado');
    Route::post('/reparaciones/{reparacion}/enviar-cotizacion', [ReparacionesController::class, 'enviarCotizacionCliente'])->name('reparaciones.enviar-cotizacion');
    Route::post('/reparaciones/{id}/tomar-trabajo', [ReparacionesController::class, 'tomarTrabajo'])->name('reparaciones.tomar-trabajo');
    Route::post('/reparaciones/{id}/completar-trabajo', [ReparacionesController::class, 'completarTrabajo'])->name('reparaciones.completar-trabajo');
    Route::get('/reparaciones/{reparacion}/etiqueta-entrada', [ReparacionesController::class, 'etiquetaEntrada'])->name('reparaciones.etiqueta-entrada');

    Route::resource('clientes', ClientesController::class);

    Route::resource('equipos', EquiposController::class);
    Route::get('/equipos/{equipo}/historial', [EquiposController::class, 'historial'])->name('equipos.historial');
    Route::post('/equipos/{equipo}/actualizar', [EquiposController::class, 'actualizar'])->name('equipos.actualizar');

    Route::get('/actividad-usuarios', [ActividadUsuarioController::class, 'index'])->name('actividad-usuarios.index');
    Route::get('/actividad-usuarios/{id}', [ActividadUsuarioController::class, 'show'])->name('actividad-usuarios.show');

    Route::middleware('role:administrador')->group(function () {
        Route::get('/contabilidad', [ContabilidadController::class, 'index'])->name('contabilidad.index');
        Route::get('/contabilidad/reportes', [ContabilidadController::class, 'reportes'])->name('contabilidad.reportes');
        Route::get('/contabilidad/export/{tipo}', [ContabilidadController::class, 'export'])->name('contabilidad.export');

        Route::get('/facturas/configuracion', [FacturaConfiguracionController::class, 'index'])->name('facturas.configuracion');
        Route::match(['put', 'patch', 'post'], '/facturas/configuracion', [FacturaConfiguracionController::class, 'update'])->name('facturas.configuracion.update');

        Route::get('/sistema/configuracion', [SistemaConfiguracionController::class, 'index'])->name('sistema.configuracion');
        Route::put('/sistema/configuracion', [SistemaConfiguracionController::class, 'update'])->name('sistema.configuracion.update');
        Route::get('/sistema/configuracion/probar-impresion', [SistemaConfiguracionController::class, 'probarImpresion'])->name('sistema.configuracion.probar-impresion');

        Route::resource('usuarios', UsuariosController::class);

        Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
        Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->where('factura', '[0-9]+')->name('facturas.show');
        Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->where('factura', '[0-9]+')->name('facturas.pdf');
        Route::get('/facturas/{factura}/whatsapp', [FacturaController::class, 'compartirWhatsApp'])->where('factura', '[0-9]+')->name('facturas.whatsapp');
        Route::get('/facturas/{factura}/edit', [FacturaController::class, 'edit'])->where('factura', '[0-9]+')->name('facturas.edit');
        Route::put('/facturas/{factura}', [FacturaController::class, 'update'])->where('factura', '[0-9]+')->name('facturas.update');
    });

    Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Rutas publicas (sin autenticacion) - Consulta de estado para clientes
$consultaPrefix = '';
if (!app()->runningInConsole() && str_starts_with(request()->path(), 'martinez/public')) {
    $consultaPrefix = 'martinez/public';
}

Route::prefix($consultaPrefix)->group(function () {
    Route::get('/consulta', [PublicController::class, 'formularioConsulta'])->name('public.consulta.form');
    Route::get('/consulta/{codigo}', [PublicController::class, 'vistaConsulta'])->name('public.consulta');
});
