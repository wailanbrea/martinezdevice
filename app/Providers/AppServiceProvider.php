<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Reparacion;
use App\Observers\ReparacionObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar observer para limpiar caché automáticamente
        Reparacion::observe(ReparacionObserver::class);
    }
}
