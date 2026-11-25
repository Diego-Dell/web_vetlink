@extends('layouts.app')
@section('titulo', 'Gestionar ganado')
@section('contenido')
    <form class="filtros" method="get">
        <label class="campo"><x-icono nombre="buscar" tam="18" /><input name="buscar" value="{{ $buscar }}" maxlength="60" placeholder="Identificación o nombre"></label>
        <div class="pildoras">
            @foreach (['activos' => 'Activos', 'baja' => 'De baja', 'todos' => 'Todos'] as $clave => $texto)
                <a class="pildora {{ $estado === $clave ? 'activa' : '' }}" href="{{ route('ganado', ['estado' => $clave, 'buscar' => $buscar ?: null]) }}">{{ $texto }}</a>
            @endforeach
        </div>
    </form>
    <div class="suave chico">{{ $fichas->count() }} {{ $fichas->count() === 1 ? 'animal' : 'animales' }}</div>
    @forelse ($fichas as $f)
        @php($t = ($tratamientos[$f['id']] ?? collect())->first())
        <a class="tarjeta" href="{{ route('ficha', $f['id']) }}" style="color:inherit">
            <div style="display:flex;gap:12px;align-items:center">
                <span class="burbuja"><x-icono nombre="ganado" /></span>
                <div class="textos" style="flex:1;min-width:0"><strong>{{ $f['identificacion'] }}{{ $f['nombre'] ? ' · '.$f['nombre'] : '' }}</strong><span class="suave chico">{{ ucfirst($f['etapa']) }}{{ $f['raza'] ? ' · '.$f['raza'] : '' }}</span></div>
                @if ($f['fecha_baja'])<span class="chip neutro">De baja</span>@else<span class="chip">Activa</span>@endif
            </div>
            @if ($t)
                <div class="suave chico" style="margin-top:8px">{{ $t['medicamento'] }}</div>
                @include('partials.avance', ['avance' => $t['avance']])
            @endif
        </a>
    @empty
        <div class="tarjeta vacio">No hay animales con ese filtro.</div>
    @endforelse
@endsection
