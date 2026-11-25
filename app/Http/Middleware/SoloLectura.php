<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloLectura
{
    private const PERMITIDAS = ['login.entrar', 'logout'];

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethodSafe() && ! in_array($request->route()?->getName(), self::PERMITIDAS, true)) {
            abort(405, 'La web de VetLink es solo de consulta. Los registros se hacen desde la app.');
        }

        return $next($request);
    }
}
