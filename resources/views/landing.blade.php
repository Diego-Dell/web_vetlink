@extends('layouts.base')
@section('titulo', 'El cuidado de tu ganado')
@section('cuerpo')
@php($destino = session('vetlink') ? route('inicio') : route('login'))
@php($accion = session('vetlink') ? 'Ir a mi granja' : 'Iniciar sesión')
<div class="land">
    {{-- 1. Barra: sobre la foto en el celular, arriba en PC. --}}
    <header class="land-nav">
        <a class="marca" href="{{ route('landing') }}"><img src="{{ asset('img/logo.png') }}" alt="">VetLink</a>
        <a class="enlace" href="#incluye">Qué podés ver</a>
        <a class="boton" href="{{ $destino }}">{{ $accion }}</a>
    </header>

    {{-- 2. Portada: foto y mensaje; en PC quedan lado a lado. --}}
    <section class="land-hero">
        <div class="land-foto" role="img" aria-label="Vaca en el campo"></div>
        <div class="land-texto">
            <span class="chip" style="justify-self:start">Web de consulta</span>
            <h1>El cuidado de tu ganado, en un mismo lugar.</h1>
            <p class="suave">Consultá desde la computadora o el celular el historial sanitario de tu granja: fichas, tratamientos con sus dosis y prevención, con la misma cuenta de la app.</p>
            <div class="botones">
                <a class="boton" href="{{ $destino }}">{{ $accion }}</a>
                <a class="boton secundario" href="#incluye">Ver qué incluye</a>
            </div>
            <div class="chips">
                @foreach (['Dueño', 'Veterinario', 'Trabajador', 'Administrador'] as $rol)<span class="chip neutro">{{ $rol }}</span>@endforeach
            </div>
        </div>
    </section>

    {{-- 3. Qué se puede consultar. --}}
    <section class="land-rasgos" id="incluye">
        @foreach ([
            ['ganado', 'Gestionar ganado', 'Fichas activas y dadas de baja, con búsqueda.'],
            ['medicamento', 'Seguimiento veterinario', 'Tratamientos con dosis aplicadas y pendientes.'],
            ['vacuna', 'Prevención', 'Vacunas, desparasitaciones y controles.'],
            ['reportes', 'Generar reportes', 'Resumen por semana, mes o trimestre en PDF.'],
        ] as [$icono, $titulo, $texto])
            <div class="tarjeta fila">
                <span class="burbuja"><x-icono :nombre="$icono" /></span>
                <div class="textos"><strong>{{ $titulo }}</strong><span class="suave chico">{{ $texto }}</span></div>
            </div>
        @endforeach
        <div class="aviso"><x-icono nombre="candado" tam="18" /><span>Solo para cuentas de VetLink. La web es de consulta: los registros se crean y corrigen desde la app móvil.</span></div>
    </section>
    <footer class="land-pie">VetLink · {{ now()->year }}</footer>
</div>
@endsection
