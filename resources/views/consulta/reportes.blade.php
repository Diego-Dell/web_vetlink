@extends('layouts.app')
@section('titulo', 'Generar reportes')
@section('subtitulo', 'Resumen sanitario de '.$granja['nombre'].' desde el '.\Illuminate\Support\Carbon::parse($desde)->format('d/m/Y'))
@section('contenido')
    {{-- 1. Período por GET; el PDF sale de la impresión del navegador (Guardar como PDF). --}}
    <div class="filtros no-imprimir">
        <div class="pildoras">
            @foreach (['semana' => 'Semana', 'mes' => 'Mes', 'trimestre' => 'Trimestre'] as $clave => $texto)
                <a class="pildora {{ $periodo === $clave ? 'activa' : '' }}" href="{{ route('reportes', ['periodo' => $clave]) }}">{{ $texto }}</a>
            @endforeach
        </div>
        <button class="boton angosto-pc solo-pc" onclick="window.print()"><x-icono nombre="reportes" tam="18" />Descargar PDF</button>
    </div>
    <div class="suave chico solo-movil">Resumen sanitario de {{ $granja['nombre'] }} desde el {{ \Illuminate\Support\Carbon::parse($desde)->format('d/m/Y') }}</div>
    <div class="cifras">
        @include('partials.kpi', ['icono' => 'medicamento', 'nombre' => 'Tratamientos', 'valor' => $tratamientos->count()])
        @include('partials.kpi', ['icono' => 'check', 'nombre' => 'Aplicaciones', 'valor' => $aplicadas.($indicadas ? '/'.$indicadas : '')])
        @include('partials.kpi', ['icono' => 'vacuna', 'nombre' => 'Prevenciones', 'valor' => $prevenciones->count()])
        @include('partials.kpi', ['icono' => 'calendario', 'nombre' => 'Realizadas', 'valor' => $prevenciones->where('estado', 'realizada')->count()])
    </div>

    <div class="columnas mitad">
        {{-- 2. Aplicaciones por semana. --}}
        <section class="tarjeta">
            @php($maximo = max(1, $semanas->max()))
            <div class="fila"><strong style="flex:1">Aplicaciones por semana</strong><span class="suave chico">{{ $semanas->sum() }} en total</span></div>
            <div class="grafico">
                @foreach ($semanas as $lunes => $cantidad)
                    <div class="barra-dato {{ $loop->last ? 'ultima' : '' }}">
                        {{ $cantidad }}
                        <span class="b" style="height: {{ round($cantidad * 100 / $maximo) }}px"></span>
                        <span>{{ \Illuminate\Support\Carbon::parse($lunes)->format('d/m') }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- 3. Tratamientos del período con su avance. --}}
        <section class="columna">
            <div class="grupo">Tratamientos del período</div>
            @forelse ($tratamientos as $t)
                <div class="tarjeta">
                    <div class="fila"><strong style="flex:1">{{ $t['animal'] }}</strong>
                        @if ($t['fecha_fin'])<span class="chip neutro">Finalizado</span>@elseif ($t['avance']['total'] && ! $t['avance']['faltan'])<span class="chip">Completo</span>@else<span class="chip adv">En curso</span>@endif
                    </div>
                    <div class="suave chico">{{ $t['medicamento'] }} · desde {{ \Illuminate\Support\Carbon::parse($t['inicio'])->format('d/m/Y') }}</div>
                    @include('partials.avance', ['avance' => $t['avance']])
                </div>
            @empty
                <div class="tarjeta vacio">Sin tratamientos en el período.</div>
            @endforelse
        </section>
    </div>
    <button class="boton no-imprimir solo-movil" onclick="window.print()"><x-icono nombre="reportes" tam="18" />Descargar PDF</button>
@endsection
