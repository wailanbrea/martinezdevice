<?php

namespace App\Observers;

use App\Models\Reparacion;
use Illuminate\Support\Facades\Cache;

class ReparacionObserver
{
    /**
     * Limpiar caché cuando se crea, actualiza o elimina una reparación
     */
    public function saved(Reparacion $reparacion)
    {
        $this->clearDashboardCache();
    }

    public function deleted(Reparacion $reparacion)
    {
        $this->clearDashboardCache();
    }

    protected function clearDashboardCache()
    {
        Cache::forget('dashboard.siguiente_mantenimiento');
        Cache::forget('dashboard.siguiente_reparacion');
        Cache::forget('dashboard.siguiente_gpu');
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.reparaciones_por_mes');
    }
}

