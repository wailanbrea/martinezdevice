<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleAuth
{
    /**
     * Middleware simple de autenticación sin usar Auth facade
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('authenticated')) {
            return redirect()->route('login');
        }

        return $next($request);
    }
}

