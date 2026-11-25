@extends('layouts.app')
@section('titulo', 'Perfil')
@section('subtitulo', 'Tu cuenta y tus granjas')
@section('contenido')
    <div class="columnas perfil">
        {{-- 1. Cuenta y cerrar sesión. --}}
        <section class="columna">
            <div class="tarjeta perfil-tarjeta">
                <span class="burbuja grande circulo"><x-icono nombre="perfil" tam="28" /></span>
                <h2 style="font-size:18px">{{ session('vetlink.nombre') }}</h2>
                <span class="suave chico">{{ session('vetlink.correo') }}</span>
            </div>
            <div class="aviso"><x-icono nombre="info" tam="18" /><span>Para cambiar la contraseña o registrar datos usá la app móvil.</span></div>
            <form method="post" action="{{ route('logout') }}">@csrf<button class="boton secundario peligro"><x-icono nombre="cerrar_sesion" tam="18" />Cerrar sesión</button></form>
        </section>
        {{-- 2. Granjas vinculadas; tocar una la elige. --}}
        <section class="columna">
            <div class="grupo">Mis granjas</div>
            @forelse ($granjas as $g)
                <a class="tarjeta fila" href="{{ route('granja', $g['id']) }}">
                    <span class="burbuja"><x-icono nombre="granja" /></span>
                    <div class="textos"><strong>{{ $g['nombre'] }}</strong><span class="suave chico">{{ ucfirst($g['rol']) }}</span></div>
                    @if (($granja['id'] ?? null) === $g['id'])<span class="chip">Actual</span>@else<x-icono nombre="flecha" tam="16" class="suave" />@endif
                </a>
            @empty
                <div class="tarjeta vacio">Sin granjas confirmadas.</div>
            @endforelse
        </section>
    </div>
@endsection
