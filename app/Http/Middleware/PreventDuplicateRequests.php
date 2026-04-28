<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo aplicar a métodos POST, PUT, PATCH, DELETE
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $next($request);
        }

        // Excluir configuración de facturas y sistema (formularios que se guardan explícitamente)
        $path = $request->path();
        if (str_ends_with($path, 'facturas/configuracion') || str_ends_with($path, 'sistema/configuracion')) {
            return $next($request);
        }

        // Crear un hash único basado en la petición
        $key = $this->getRequestHash($request);

        // Verificar si ya existe una petición en proceso
        if (Cache::has($key)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Ya existe una petición en proceso. Por favor, espere un momento.',
                    'error' => 'duplicate_request'
                ], 429)->header('Retry-After', '2');
            }
            
            // Para requests HTML, redirigir de vuelta con mensaje
            return back()->with('error', 'Ya existe una petición en proceso. Por favor, espere un momento antes de intentar nuevamente.')
                ->withInput();
        }

        // Marcar la petición como en proceso (expira en 5 segundos)
        Cache::put($key, true, 5);

        try {
            $response = $next($request);
            
            // Si la respuesta es exitosa (2xx), mantener el lock un poco más para prevenir doble submit
            if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                Cache::put($key, true, 10);
            }
            
            return $response;
        } finally {
            // El cache se limpiará automáticamente después del tiempo de expiración
        }
    }

    /**
     * Generar un hash único para la petición
     */
    private function getRequestHash(Request $request): string
    {
        $data = [
            $request->method(),
            $request->path(),
            $request->ip(),
            auth()->id() ?? 'guest',
            $request->header('User-Agent'),
            md5(json_encode($request->except(['_token', '_method', 'password', 'password_confirmation']))),
        ];

        return 'request_lock:' . md5(implode('|', $data));
    }
}
