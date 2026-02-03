<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSystemHours
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Definimos la Zona Horaria (Santiago del Estero/Argentina)
        $timezone = 'America/Argentina/Buenos_Aires';

        // 2. Obtenemos la hora actual en esa zona
        $now = Carbon::now($timezone);

        // 3. Definimos Hora de Apertura (08:00) y Cierre (23:59)
        $startTime = Carbon::createFromTime(8, 0, 0, $timezone);
        $endTime = Carbon::createFromTime(23, 59, 59, $timezone);

        // 4. Lógica de Bloqueo
        // Si la hora actual es MENOR a la apertura O MAYOR al cierre...
        if ($now->lt($startTime) || $now->gt($endTime)) {

            // EXCEPCIÓN: Si ya está intentando ver la vista de "Cerrado", lo dejamos pasar
            // para evitar un bucle infinito de redirecciones.
            if ($request->routeIs('system.closed')) {
                return $next($request);
            }

            // A cualquier otro intento, lo mandamos a la vista de cerrado
            return redirect()->route('system.closed');
        }

        // 5. Lógica Inversa: Si el sistema está ABIERTO pero intenta ver la vista de "Cerrado"
        if ($request->routeIs('system.closed')) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
