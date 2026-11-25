<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// 1. Facade de Supabase: iniciar sesión con Auth y leer tablas con el token de la persona (RLS).
class Supabase
{
    private function url(): string
    {
        return config('vetlink.supabase_url');
    }

    private function cliente(): PendingRequest
    {
        return Http::withHeaders(['apikey' => config('vetlink.supabase_key')])
            ->acceptJson()
            ->timeout(config('vetlink.timeout'));
    }

    // 2. Inicia sesión con correo y contraseña. Devuelve la sesión o un mensaje de error.
    public function iniciarSesion(string $correo, string $contrasena): array
    {
        if ($this->url() === '' || config('vetlink.supabase_key') === '') {
            // El detalle técnico queda en el registro del servidor; la persona ve un mensaje simple.
            Log::error('VetLink web: faltan SUPABASE_URL o SUPABASE_PUBLISHABLE_KEY en las variables de entorno.');

            return ['error' => 'El inicio de sesión no está disponible por el momento. Intentá más tarde.'];
        }
        try {
            $respuesta = $this->cliente()->post($this->url().'/auth/v1/token?grant_type=password', [
                'email' => $correo,
                'password' => $contrasena,
            ]);
        } catch (ConnectionException) {
            return ['error' => 'No se pudo conectar. Revisá tu internet'];
        }
        if ($respuesta->failed()) {
            return ['error' => 'Correo o contraseña incorrectos'];
        }

        return $this->sesionDesde($respuesta->json());
    }

    // 3. Renueva el token vencido.
    public function renovar(string $refresh): ?array
    {
        try {
            $respuesta = $this->cliente()->post($this->url().'/auth/v1/token?grant_type=refresh_token', [
                'refresh_token' => $refresh,
            ]);
        } catch (ConnectionException) {
            return null;
        }

        return $respuesta->successful() ? $this->sesionDesde($respuesta->json()) : null;
    }

    // 4. Cierra la sesión también en Supabase.
    public function cerrarSesion(string $token): void
    {
        try {
            $this->cliente()->withToken($token)->post($this->url().'/auth/v1/logout');
        } catch (ConnectionException) {
        }
    }

    // 5. Única operación sobre los datos: lectura (GET). La web nunca crea, modifica ni borra.
    public function leer(string $token, string $tabla, array $filtros = []): array
    {
        $respuesta = $this->cliente()->withToken($token)->get($this->url().'/rest/v1/'.$tabla, $filtros);
        if ($respuesta->status() === 401) {
            throw new SesionVencida;
        }
        $respuesta->throw();

        return $respuesta->json() ?? [];
    }

    private function sesionDesde(array $datos): array
    {
        $usuario = $datos['user'] ?? [];

        return [
            'token' => $datos['access_token'],
            'refresh' => $datos['refresh_token'] ?? null,
            'vence' => time() + (int) ($datos['expires_in'] ?? 3600) - 60,
            'cuenta' => $usuario['id'] ?? null,
            'correo' => $usuario['email'] ?? null,
            'nombre' => $usuario['user_metadata']['nombre'] ?? ($usuario['email'] ?? 'Cuenta'),
        ];
    }
}
