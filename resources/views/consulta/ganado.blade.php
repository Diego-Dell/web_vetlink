@extends('layouts.app')
@section('titulo', 'Gestionar ganado')
@section('subtitulo', $fichas->count().' '.($fichas->count() === 1 ? 'animal' : 'animales').' · '.$tratamientos->count().' en tratamiento')
@section('contenido')
    {{-- 1. Búsqueda y filtro por GET: no modifican datos. --}}
    <form class="filtros" method="get">
        <input type="hidden" name="estado" value="{{ $estado }}">
        <label class="campo"><x-icono nombre="buscar" tam="18" /><input name="buscar" value="{{ $buscar }}" maxlength="60" placeholder="Buscar por identificación o nombre"></label>
        <div class="pildoras">
            @foreach (['activos' => 'Activos', 'baja' => 'De baja', 'todos' => 'Todos'] as $clave => $texto)
                <a class="pildora {{ $estado === $clave ? 'activa' : '' }}" href="{{ route('ganado', ['estado' => $clave, 'buscar' => $buscar ?: null]) }}">{{ $texto }}</a>
            @endforeach
        </div>
    </form>
    <div class="suave chico solo-movil">{{ $fichas->count() }} {{ $fichas->count() === 1 ? 'animal' : 'animales' }} · {{ $tratamientos->count() }} en tratamiento</div>

    {{-- 2. Fichas: lista en el celular y grilla de tres columnas en PC. --}}
    <div class="rejilla">
        @forelse ($fichas as $f)
            @php($t = ($tratamientos[$f['id']] ?? collect())->first())
            <a class="tarjeta" href="{{ route('ficha', $f['id']) }}">
                <div class="fila">
                    <span class="burbuja"><x-icono nombre="ganado" /></span>
                    <div class="textos"><strong>{{ $f['identificacion'] }}{{ $f['nombre'] ? ' · '.$f['nombre'] : '' }}</strong><span class="suave chico">{{ ucfirst($f['etapa']) }}{{ $f['raza'] ? ' · '.$f['raza'] : '' }}</span></div>
                    @if ($f['fecha_baja'])<span class="chip neutro">De baja</span>@else<span class="chip">Activa</span>@endif
                </div>
                @if ($t)
                    <div class="detalle"><x-icono nombre="medicamento" tam="14" />{{ $t['medicamento'] }}</div>
                    @include('partials.avance', ['avance' => $t['avance']])
                @else
                    <div class="detalle">Sin tratamiento en curso</div>
                @endif
            </a>
        @empty
            <div class="tarjeta vacio">No hay animales con ese filtro.</div>
        @endforelse
    </div>
@endsection
