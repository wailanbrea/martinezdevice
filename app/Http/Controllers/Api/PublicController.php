<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function status($codigo_unico)
    {
        $equipo = Equipo::where('codigo_unico', $codigo_unico)
            ->with(['cliente', 'historialEstados' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->first();

        if (!$equipo) {
            return response()->json([
                'error' => 'Equipo no encontrado'
            ], 404);
        }

        $ultimoMovimiento = $equipo->historialEstados->first();

        return response()->json([
            'cliente' => $equipo->cliente->nombre,
            'equipo' => $equipo->tipo . ' ' . ($equipo->marca ?? '') . ' ' . ($equipo->modelo ?? ''),
            'estado' => $this->estadoTraducido($equipo->estado),
            'ultimo_movimiento' => $ultimoMovimiento ? $ultimoMovimiento->observaciones : 'Sin movimientos recientes',
            'fecha_actualizacion' => $ultimoMovimiento ? $ultimoMovimiento->updated_at->format('Y-m-d') : $equipo->updated_at->format('Y-m-d'),
            'foto' => $equipo->foto ? asset('storage/' . $equipo->foto) : null,
        ]);
    }

    private function estadoTraducido($estado)
    {
        $estados = [
            'recibido' => 'Recibido',
            'diagnostico' => 'En diagnóstico',
            'reparacion' => 'En reparación',
            'listo' => 'Listo para entregar',
            'entregado' => 'Entregado',
            'garantia' => 'En garantía',
        ];

        return $estados[$estado] ?? $estado;
    }
}
