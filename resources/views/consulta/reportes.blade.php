@extends('layouts.app')
@section('titulo', 'Generar reportes')
@section('contenido')
    <div class="pildoras no-imprimir">
        @foreach (['semana' => 'Semana', 'mes' => 'Mes', 'trimestre' => 'Trimestre'] as $clave => $texto)
            <a class="pildora {{ $periodo === $clave ? 'activa' : '' }}" href="{{ route('reportes', ['periodo' => $clave]) }}">{{ $texto }}</a>
        @endforeach
    </div>
    <div class="suave chico">Resumen sanitario de {{ $granja['nombre'] }} desde el {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }}</div>
    <div class="cifras">
        <div class="tarjeta"><div class="suave chico">Tratamientos</div><div class="cifra">{{ $tratamientos->count() }}</div></div>
        <div class="tarjeta"><div class="suave chico">Aplicaciones</div><div class="cifra">{{ $aplicadas }}{{ $indicadas ? '/'.$indicadas : '' }}</div></div>
        <div class="tarjeta"><div class="suave chico">Prevenciones</div><div class="cifra">{{ $prevenciones->count() }}</div></div>
        <div class="tarjeta"><div class="suave chico">Realizadas</div><div class="cifra">{{ $prevenciones->where('estado', 'realizada')->count() }}</div></div>
    </div>
    <div class="grupo">Tratamientos del período</div>
    @forelse ($tratamientos as $t)
        <div class="tarjeta">
            <div style="display:flex;justify-content:space-between;gap:8px">
                <strong>{{ $t['animal'] }}</strong>
                @if ($t['fecha_fin'])<span class="chip neutro">Finalizado</span>
                @elseif ($t['avance']['total'] && ! $t['avance']['faltan'])<span class="chip">Completo</span>
                @else<span class="chip adv">En curso</span>@endif
            </div>
            <div class="suave chico">{{ $t['medicamento'] }} · desde {{ \Illuminate\Support\Carbon::parse($t['inicio'])->format('d/m/Y') }}</div>
            @include('partials.avance', ['avance' => $t['avance']])
        </div>
    @empty
        <div class="tarjeta vacio">Sin tratamientos en el período.</div>
    @endforelse
    <button class="boton no-imprimir" onclick="window.print()"><x-icono nombre="reportes" tam="18" />Descargar PDF</button>
@endsection
