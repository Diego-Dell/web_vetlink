@extends('layouts.app')
@section('titulo', 'Hola, '.explode(' ', session('vetlink.nombre') ?? '')[0])
@section('subtitulo', ucfirst(now()->locale('es')->isoFormat('dddd D [de] MMMM')).' · resumen sanitario de '.$granja['nombre'])
@section('contenido')
    <div class="suave chico solo-movil">{{ ucfirst(now()->locale('es')->isoFormat('dddd D [de] MMMM')) }}</div>
    {{-- 1. Indicadores de la granja elegida. --}}
    <div class="cifras">
        @include('partials.kpi', ['icono' => 'ganado', 'nombre' => 'Animales activos', 'valor' => $activos])
        @include('partials.kpi', ['icono' => 'medicamento', 'nombre' => 'En tratamiento', 'valor' => $enTratamiento])
        @include('partials.kpi', ['icono' => 'calendario', 'nombre' => 'Dosis pendientes', 'valor' => $faltantes])
        @include('partials.kpi', ['icono' => 'vacuna', 'nombre' => 'Prevenciones', 'valor' => $pendientesPrevencion])
    </div>

    {{-- 2. Seguimiento veterinario y cuidados diarios (dos columnas en PC). --}}
    <div class="columnas">
        <section class="columna">
            <div class="grupo">Gestionar seguimiento veterinario <a href="{{ route('ganado') }}">Ver ganado</a></div>
            @forelse ($tratamientos as $t)
                @include('partials.tratamiento')
            @empty
                <div class="tarjeta vacio">No hay tratamientos en curso.</div>
            @endforelse
        </section>
        <section class="columna">
            <div class="grupo">Gestión de cuidados diarios</div>
            @forelse ($prevenciones as $p)
                @php($vencida = $p['fecha_programada'] < now()->toDateString())
                <div class="tarjeta fila">
                    <span class="burbuja"><x-icono nombre="{{ $p['tipo'] === 'control' ? 'calendario' : 'vacuna' }}" /></span>
                    <div class="textos"><strong>{{ $p['nombre'] }}</strong><span class="suave chico">{{ $nombres[$p['ficha_id']]['identificacion'] ?? '' }} · {{ \Illuminate\Support\Carbon::parse($p['fecha_programada'])->format('d/m/Y') }}</span></div>
                    <span class="chip {{ $vencida ? 'err' : 'adv' }}">{{ $vencida ? 'Vencida' : 'Pendiente' }}</span>
                </div>
            @empty
                <div class="tarjeta vacio">Sin prevenciones pendientes.</div>
            @endforelse
        </section>
    </div>
@endsection
