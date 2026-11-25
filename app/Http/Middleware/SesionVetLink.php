<?php

namespace App\Http\Middleware;

use App\Services\Supabase;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SesionVetLink
{
    public function __construct(private Supabase $api) {}

    public function handle(Request $request, Closure $next): Response
    {
        $sesion = $request->session()->get('vetlink');
        if (! $sesion) {
            return redirect()->route('login')->with('aviso', 'Necesitás iniciar sesión con tu cuenta de VetLink.');
        }
        if ($sesion['vence'] < time()) {
            $nueva = $sesion['refresh'] ? $this->api->renovar($sesion['refresh']) : null;
            if (! $nueva) {
                $request->session()->forget('vetlink');

                return redirect()->route('login')->with('aviso', 'Tu sesión venció. Volvé a iniciar sesión.');
            }
            $request->session()->put('vetlink', $nueva);
        }

        return $next($request);
    }
}
