<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Si el usuario es administrador, tiene acceso a todo
        if (method_exists($user, 'tieneRol') && $user->tieneRol('administrador')) {
            return $next($request);
        }
        
        // Si el usuario tiene el método tieneRol
        if (method_exists($user, 'tieneRol')) {
            foreach ($roles as $role) {
                if ($user->tieneRol($role)) {
                    return $next($request);
                }
            }
        }

        abort(403, 'No tienes permisos para acceder a esta página.');
    }
}
