<?php

namespace App\Http\Controllers;

use App\Services\Supabase;
use Illuminate\Http\Request;

class AccesoController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function formulario(Request $request)
    {
        return $request->session()->has('vetlink') ? redirect()->route('inicio') : view('acceso.login');
    }

    public function entrar(Request $request, Supabase $supabase)
    {
        $datos = $request->validate([
            'correo' => ['required', 'email', 'max:254'],
            'contrasena' => ['required', 'string', 'max:128'],
        ], [
            'correo.required' => 'Ingresá tu correo electrónico.',
            'correo.email' => 'Ingresá un correo válido.',
            'contrasena.required' => 'Ingresá tu contraseña.',
        ]);

        $sesion = $supabase->iniciarSesion(trim($datos['correo']), $datos['contrasena']);
        if (isset($sesion['error'])) {
            return back()->withInput(['correo' => $datos['correo']])->withErrors(['correo' => $sesion['error']]);
        }
        $request->session()->regenerate();
        $request->session()->put('vetlink', $sesion);

        return redirect()->intended(route('inicio'));
    }

    public function salir(Request $request, Supabase $supabase)
    {
        if ($token = $request->session()->get('vetlink.token')) {
            $supabase->cerrarSesion($token);
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('aviso', 'Cerraste sesión.');
    }
}
