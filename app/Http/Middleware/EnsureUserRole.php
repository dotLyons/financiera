<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Verificamos si el usuario tiene el rol requerido
        if ($request->user() && $request->user()->role === $role) {
            return $next($request);
        }

        // 2. Si no es el rol correcto, abortamos con error 403 (Forbidden)
        abort(403, 'No tienes autorización para ver esta página.');
    }
}
