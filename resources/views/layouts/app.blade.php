@extends('layouts.base')
@section('cuerpo')
@php($secciones = [['inicio', 'Inicio', 'Inicio', 'inicio'], ['ganado', 'Ganado', 'Gestionar ganado', 'ganado'], ['reportes', 'Reportes', 'Generar reportes', 'reportes'], ['perfil', 'Perfil', 'Perfil', 'perfil']])
@php($activa = fn ($ruta) => request()->routeIs($ruta, $ruta === 'ganado' ? 'ficha' : $ruta))
@php($misGranjas = isset($granja) ? app(\App\Services\Consulta::class)->granjas() : collect())
<div class="app">
    {{-- 1. PC: menú lateral con las secciones, aviso de solo lectura y la cuenta. --}}
    <aside class="lateral">
        <div class="marca"><img src="{{ asset('img/logo.png') }}" alt=""><div><strong>VetLink</strong><span class="suave chico">Web de consulta</span></div></div>
        <nav>
            @foreach ($secciones as [$ruta, $corto, $largo, $icono])
                <a href="{{ route($ruta) }}" class="{{ $activa($ruta) ? 'activa' : '' }}"><x-icono :nombre="$icono" />{{ $largo }}</a>
            @endforeach
        </nav>
        <div class="pie">
            <div class="nota"><x-icono nombre="candado" tam="16" /><span>Solo lectura: los registros se hacen en la app.</span></div>
            <div class="yo">
                <span class="burbuja chica circulo"><x-icono nombre="perfil" tam="18" /></span>
                <div class="textos"><strong class="chico">{{ session('vetlink.nombre') }}</strong><span class="suave chico">{{ session('vetlink.correo') }}</span></div>
                <form method="post" action="{{ route('logout') }}">@csrf<button title="Cerrar sesión"><x-icono nombre="cerrar_sesion" tam="18" /></button></form>
            </div>
        </div>
    </aside>

    <div class="principal">
        {{-- 2. Encabezado: en el celular logo o volver y selector de granja; en PC título, detalle y granja. --}}
        <header class="cabecera">
            @hasSection('volver')
                <a class="redondo" href="@yield('volver')" aria-label="Volver"><x-icono nombre="volver" /></a>
            @else
                <img class="logo" src="{{ asset('img/logo.png') }}" alt="VetLink">
            @endif
            <div class="titulo">
                <h1>@yield('titulo')</h1>
                <span class="subtitulo">@yield('subtitulo')</span>
                @isset($granja)
                    @include('partials.granjas', ['clase' => ''])
                @endisset
            </div>
            @isset($granja)
                @include('partials.granjas', ['clase' => 'granjas-pc'])
            @endisset
            <span class="candado" title="Solo lectura"><x-icono nombre="candado" tam="16" /></span>
        </header>
        <main class="contenido">
            @yield('contenido')
        </main>
    </div>

    {{-- 3. Celular: barra inferior con las mismas secciones. --}}
    <nav class="barra">
        @foreach ($secciones as [$ruta, $corto, $largo, $icono])
            <a href="{{ route($ruta) }}" class="{{ $activa($ruta) ? 'activa' : '' }}"><span class="pastilla"><x-icono :nombre="$icono" /></span>{{ $corto }}</a>
        @endforeach
    </nav>
</div>
@endsection
