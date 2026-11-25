@extends('layouts.app')
@section('titulo', 'Hola, '.explode(' ', session('vetlink.nombre') ?? '')[0])
@section('contenido')
    <div class="cifras">
        <div class="tarjeta"><div class="suave chico">Animales activos</div><div class="cifra">{{ $activos }}</div></div>
        <div class="tarjeta"><div class="suave chico">Dosis pendientes</div><div class="cifra">{{ $faltantes }}</div></div>
    </div>

    <div class="grupo">Gestionar seguimiento veterinario</div>
    @forelse ($tratamientos as $t)
        <a class="tarjeta" href="{{ route('ficha', $t['ficha_id']) }}" style="color:inherit">
            <div class="fila" style="display:flex;gap:12px;align-items:center">
                <span class="burbuja"><x-icono nombre="medicamento" /></span>
                <div class="textos"><strong>{{ $t['medicamento'] }}</strong><span class="suave chico">{{ $t['animal'] }} · {{ \App\Support\Dosis::numero($t['dosis']) }} {{ $t['unidad'] }} cada {{ $t['frecuencia'] }}</span></div>
            </div>
            @include('partials.avance', ['avance' => $t['avance']])
        </a>
    @empty
        <div class="tarjeta vacio">No hay tratamientos en curso.</div>
    @endforelse

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
@endsection
