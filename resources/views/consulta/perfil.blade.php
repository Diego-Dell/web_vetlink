@extends('layouts.app')
@section('titulo', 'Perfil')
@section('contenido')
    <div class="tarjeta fila">
        <span class="burbuja" style="border-radius:50%"><x-icono nombre="perfil" /></span>
        <div class="textos"><strong>{{ session('vetlink.nombre') }}</strong><span class="suave chico">{{ session('vetlink.correo') }}</span></div>
    </div>
    <div class="grupo">Mis granjas</div>
    @forelse ($granjas as $g)
        <a class="tarjeta fila" href="{{ route('granja', $g['id']) }}">
            <span class="burbuja"><x-icono nombre="granja" /></span>
            <div class="textos"><strong>{{ $g['nombre'] }}</strong><span class="suave chico">{{ ucfirst($g['rol']) }}</span></div>
            @if (($granja['id'] ?? null) === $g['id'])<span class="chip">Actual</span>@endif
        </a>
    @empty
        <div class="tarjeta vacio">Sin granjas confirmadas.</div>
    @endforelse
    <div class="aviso"><x-icono nombre="info" tam="18" /><span>Para cambiar la contraseña o registrar datos usá la app móvil.</span></div>
    <form method="post" action="{{ route('logout') }}">@csrf<button class="boton secundario" style="color:var(--rojo)"><x-icono nombre="cerrar_sesion" tam="18" />Cerrar sesión</button></form>
@endsection
