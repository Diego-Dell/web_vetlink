@extends('layouts.app')
@section('titulo', $ficha['identificacion'].($ficha['nombre'] ? ' · '.$ficha['nombre'] : ''))
@section('volver', route('ganado'))
@section('contenido')
    {{-- 1. Datos de la ficha. --}}
    <div class="tarjeta">
        <dl class="datos">
            <dt>Etapa</dt><dd>{{ ucfirst($ficha['etapa']) }}</dd>
            <dt>Raza</dt><dd>{{ $ficha['raza'] ?? '—' }}</dd>
            <dt>Nacimiento</dt><dd>{{ $ficha['nacimiento'] ? \Illuminate\Support\Carbon::parse($ficha['nacimiento'])->format('d/m/Y') : '—' }}</dd>
            <dt>Estado</dt><dd>{{ $ficha['fecha_baja'] ? 'De baja' : 'Activa' }}</dd>
            @if ($ficha['fecha_baja'])<dt>Motivo de baja</dt><dd>{{ $ficha['motivo_baja'] }}</dd>@endif
        </dl>
    </div>

    {{-- 2. Tratamientos con el contador de aplicaciones. --}}
    <div class="grupo">Tratamientos</div>
    @forelse ($tratamientos as $t)
        <div class="tarjeta">
            <div style="display:flex;justify-content:space-between;gap:8px"><strong>{{ $t['medicamento'] }}</strong>@if ($t['fecha_fin'])<span class="chip neutro">Finalizado</span>@endif</div>
            <div class="suave chico">{{ \App\Support\Dosis::numero($t['dosis']) }} {{ $t['unidad'] }} · {{ $t['via'] }} · cada {{ $t['frecuencia'] }} · {{ $t['duracion'] }}{{ $t['retiro'] ? ' · retiro '.$t['retiro'] : '' }}</div>
            @include('partials.avance', ['avance' => $t['avance']])
        </div>
    @empty
        <div class="tarjeta vacio">Sin tratamientos.</div>
    @endforelse

    <div class="grupo">Aplicaciones</div>
    <div class="tarjeta" style="padding-top:6px;padding-bottom:6px">
        @forelse ($aplicaciones as $a)
            <div class="linea">
                <span>{{ \Illuminate\Support\Carbon::parse($a['fecha_hora_real'])->timezone('America/La_Paz')->format('d/m/Y H:i') }}<br><span class="suave chico">{{ $a['observacion'] ?: (strtolower($a['tipo'] ?? '') === 'prevencion' ? 'Prevención' : 'Tratamiento') }}</span></span>
                <strong>{{ \App\Support\Dosis::numero($a['dosis']) }} {{ $a['unidad'] }}</strong>
            </div>
        @empty
            <div class="vacio">Sin aplicaciones.</div>
        @endforelse
    </div>

    <div class="grupo">Historial clínico</div>
    @forelse ($atenciones as $at)
        <div class="tarjeta">
            <strong>{{ ucfirst($at['tipo']) }} · {{ \Illuminate\Support\Carbon::parse($at['fecha'])->format('d/m/Y') }}</strong>
            <div class="suave" style="white-space:pre-line;margin-top:4px">{{ $at['detalle'] }}</div>
        </div>
    @empty
        <div class="tarjeta vacio">Sin atenciones registradas.</div>
    @endforelse

    <div class="grupo">Prevención</div>
    @forelse ($prevenciones as $p)
        <div class="tarjeta fila">
            <div class="textos"><strong>{{ $p['nombre'] }}</strong><span class="suave chico">{{ \Illuminate\Support\Carbon::parse($p['fecha_programada'])->format('d/m/Y') }}{{ isset($p['dosis']) && $p['dosis'] ? ' · '.\App\Support\Dosis::numero($p['dosis']).' '.$p['unidad'] : '' }}</span></div>
            <span class="chip {{ $p['estado'] === 'pendiente' ? 'adv' : ($p['estado'] === 'cancelada' ? 'neutro' : '') }}">{{ ucfirst($p['estado']) }}</span>
        </div>
    @empty
        <div class="tarjeta vacio">Sin prevenciones.</div>
    @endforelse
@endsection
