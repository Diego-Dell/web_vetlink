@extends('layouts.base')
@section('cuerpo')
<div class="movil">
    {{-- 1. Encabezado como en la app: logo o volver, título y granja elegida. --}}
    <header class="cabecera">
        @hasSection('volver')
            <a class="volver" href="@yield('volver')" aria-label="Volver"><x-icono nombre="volver" /></a>
        @else
            <img class="logo" src="{{ asset('img/logo.png') }}" alt="VetLink">
        @endif
        <div class="titulo">
            <h1>@yield('titulo')</h1>
            @isset($granja)
                <details class="granjas">
                    <summary>{{ $granja['nombre'] }} · {{ ucfirst($granja['rol'] ?? '') }} ›</summary>
                    <ul>
                        @foreach (app(\App\Services\Consulta::class)->granjas() as $g)
                            <li><a class="{{ $g['id'] === $granja['id'] ? 'activa' : '' }}" href="{{ route('granja', $g['id']) }}">{{ $g['nombre'] }}</a></li>
                        @endforeach
                    </ul>
                </details>
            @endisset
        </div>
        <span class="chip neutro">Solo lectura</span>
    </header>
    <main class="contenido">
        @yield('contenido')
    </main>
    {{-- 2. Barra inferior: una sola forma de llegar a cada sección. --}}
    <nav class="barra">
        @foreach ([['inicio', 'Inicio', 'inicio'], ['ganado', 'Ganado', 'ganado'], ['reportes', 'Reportes', 'reportes'], ['perfil', 'Perfil', 'perfil']] as [$ruta, $texto, $icono])
            <a href="{{ route($ruta) }}" class="{{ request()->routeIs($ruta, $ruta === 'ganado' ? 'ficha' : $ruta) ? 'activa' : '' }}">
                <span class="pastilla"><x-icono :nombre="$icono" /></span>{{ $texto }}
            </a>
        @endforeach
    </nav>
</div>
@endsection
