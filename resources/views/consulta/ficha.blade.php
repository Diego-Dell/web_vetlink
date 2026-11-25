@extends('layouts.app')
@section('titulo', $ficha['identificacion'].($ficha['nombre'] ? ' · '.$ficha['nombre'] : ''))
@section('subtitulo', ucfirst($ficha['etapa']).($ficha['raza'] ? ' · '.$ficha['raza'] : '').($ficha['nacimiento'] ? ' · nacida el '.\Illuminate\Support\Carbon::parse($ficha['nacimiento'])->format('d/m/Y') : ''))
@section('volver', route('ganado'))
@section('contenido')
    {{-- 1. Resumen del animal (celular). --}}
    <div class="tarjeta fila solo-movil">
        <span class="burbuja grande"><x-icono nombre="ganado" tam="26" /></span>
        <div class="textos">
            <span class="suave chico">{{ $ficha['nacimiento'] ? 'Nacida el '.\Illuminate\Support\Carbon::parse($ficha['nacimiento'])->format('d/m/Y') : 'Sin fecha de nacimiento' }}</span>
            <div class="chips" style="margin-top:6px">
                <span class="chip neutro">{{ ucfirst($ficha['etapa']) }}</span>
                @if ($ficha['raza'])<span class="chip neutro">{{ $ficha['raza'] }}</span>@endif
                @if ($ficha['fecha_baja'])<span class="chip neutro">De baja</span>@else<span class="chip">Activa</span>@endif
            </div>
        </div>
    </div>

    <div class="columnas">
        {{-- 2. Tratamientos con el contador y aplicaciones registradas. --}}
        <section class="columna">
            <div class="grupo">Tratamientos</div>
            @forelse ($tratamientos as $t)
                <div class="tarjeta">
                    <div class="fila"><strong style="flex:1">{{ $t['medicamento'] }}</strong>
                        @if ($t['fecha_fin'])<span class="chip neutro">Finalizado</span>@elseif ($t['avance']['total'] && ! $t['avance']['faltan'])<span class="chip">Completo</span>@else<span class="chip adv">En curso</span>@endif
                    </div>
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
                        <span style="color:var(--verde);display:inline-flex"><x-icono nombre="check" tam="16" /></span>
                        <div class="textos">{{ \Illuminate\Support\Carbon::parse($a['fecha_hora_real'])->timezone('America/La_Paz')->format('d/m/Y H:i') }}<br><span class="suave chico">{{ $a['observacion'] ?: (strtolower($a['tipo'] ?? '') === 'prevencion' ? 'Prevención' : 'Tratamiento') }}</span></div>
                        <strong>{{ \App\Support\Dosis::numero($a['dosis']) }} {{ $a['unidad'] }}</strong>
                    </div>
                @empty
                    <div class="vacio" style="padding:12px">Sin aplicaciones.</div>
                @endforelse
            </div>
        </section>

        {{-- 3. Datos, historial clínico y prevención. --}}
        <section class="columna">
            <div class="grupo">Datos de la ficha</div>
            <div class="tarjeta">
                <dl class="datos">
                    <dt>Identificación</dt><dd>{{ $ficha['identificacion'] }}</dd>
                    <dt>Etapa</dt><dd>{{ ucfirst($ficha['etapa']) }}</dd>
                    <dt>Raza</dt><dd>{{ $ficha['raza'] ?? '—' }}</dd>
                    <dt>Nacimiento</dt><dd>{{ $ficha['nacimiento'] ? \Illuminate\Support\Carbon::parse($ficha['nacimiento'])->format('d/m/Y') : '—' }}</dd>
                    <dt>Estado</dt><dd>{{ $ficha['fecha_baja'] ? 'De baja' : 'Activa' }}</dd>
                    @if ($ficha['fecha_baja'])<dt>Motivo de baja</dt><dd>{{ $ficha['motivo_baja'] }}</dd>@endif
                </dl>
            </div>

            <div class="grupo">Historial clínico</div>
            @forelse ($atenciones as $at)
                <div class="tarjeta fila" style="align-items:flex-start">
                    <span class="burbuja"><x-icono nombre="clinica" /></span>
                    <div class="textos"><strong>{{ ucfirst($at['tipo']) }} · {{ \Illuminate\Support\Carbon::parse($at['fecha'])->format('d/m/Y') }}</strong><span class="suave chico" style="white-space:pre-line">{{ $at['detalle'] }}</span></div>
                </div>
            @empty
                <div class="tarjeta vacio">Sin atenciones registradas.</div>
            @endforelse

            <div class="grupo">Prevención</div>
            @forelse ($prevenciones as $p)
                <div class="tarjeta fila">
                    <span class="burbuja"><x-icono nombre="{{ $p['tipo'] === 'control' ? 'calendario' : 'vacuna' }}" /></span>
                    <div class="textos"><strong>{{ $p['nombre'] }}</strong><span class="suave chico">{{ \Illuminate\Support\Carbon::parse($p['fecha_programada'])->format('d/m/Y') }}{{ ! empty($p['dosis']) ? ' · '.\App\Support\Dosis::numero($p['dosis']).' '.$p['unidad'] : '' }}</span></div>
                    <span class="chip {{ $p['estado'] === 'pendiente' ? 'adv' : ($p['estado'] === 'cancelada' ? 'neutro' : '') }}">{{ ucfirst($p['estado']) }}</span>
                </div>
            @empty
                <div class="tarjeta vacio">Sin prevenciones.</div>
            @endforelse
        </section>
    </div>
@endsection
