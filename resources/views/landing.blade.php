@extends('layouts.base')
@section('titulo', 'El cuidado de tu ganado')
@section('cuerpo')
<div class="movil landing" style="padding-bottom:0">
    {{-- 1. Portada con la marca y acceso a Iniciar sesión. --}}
    <section class="portada">
        <div class="marca">
            <div><img src="{{ asset('img/logo.png') }}" alt="">VetLink</div>
            <a href="{{ session('vetlink') ? route('inicio') : route('login') }}">{{ session('vetlink') ? 'Mi granja' : 'Iniciar sesión' }}</a>
        </div>
    </section>
    <section class="hoja">
        <h1>El cuidado de tu ganado, en un mismo lugar.</h1>
        <p class="suave" style="margin:0">Fichas, tratamientos y avisos para dueños, veterinarios y trabajadores. Desde la web consultás el historial sanitario de tu granja con la misma cuenta de la app.</p>
        <a class="boton" href="{{ session('vetlink') ? route('inicio') : route('login') }}">{{ session('vetlink') ? 'Ir a mi granja' : 'Iniciar sesión' }}</a>

        {{-- 2. Qué se puede consultar. --}}
        <div class="grupo">Qué podés ver</div>
        <div class="rasgos">
            @foreach ([
                ['ganado', 'Gestionar ganado', 'Fichas de cada animal, activas y dadas de baja.'],
                ['medicamento', 'Seguimiento veterinario', 'Tratamientos con dosis aplicadas y pendientes.'],
                ['vacuna', 'Prevención', 'Vacunas, desparasitaciones y controles programados.'],
                ['reportes', 'Generar reportes', 'Resumen sanitario por semana, mes o trimestre en PDF.'],
            ] as [$icono, $titulo, $texto])
                <div class="tarjeta fila">
                    <span class="burbuja"><x-icono :nombre="$icono" /></span>
                    <div class="textos"><strong>{{ $titulo }}</strong><span class="suave chico">{{ $texto }}</span></div>
                </div>
            @endforeach
        </div>

        {{-- 3. Reglas de acceso. --}}
        <div class="aviso"><x-icono nombre="candado" tam="18" /><span>Solo para cuentas de VetLink. La web es de consulta: los registros se crean y corrigen desde la app móvil.</span></div>
    </section>
    <footer>VetLink · {{ now()->year }}</footer>
</div>
@endsection
