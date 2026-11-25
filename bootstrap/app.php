<?php

use App\Http\Middleware\SesionVetLink;
use App\Http\Middleware\SoloLectura;
use App\Services\SesionVencida;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Render atiende HTTPS en su proxy; la web es de solo lectura y las consultas exigen sesión.
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [SoloLectura::class]);
        $middleware->alias(['vetlink.sesion' => SesionVetLink::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        // 2. Token rechazado por Supabase: se pide iniciar sesión de nuevo.
        $exceptions->render(function (SesionVencida $e, Request $request) {
            $request->session()->forget('vetlink');

            return redirect()->route('login')->with('aviso', 'Tu sesión venció. Volvé a iniciar sesión.');
        });
        // 3. Supabase sin respuesta: mensaje claro en vez de un error 500.
        $exceptions->render(fn (ConnectionException $e) => response()->view('errors.api', [], 503));
    })->create();
