@extends('layouts.base')
@section('titulo', 'Iniciar sesión')
@section('cuerpo')
<div class="land acceso">
    <header class="land-nav">
        <a class="marca" href="{{ route('landing') }}"><img src="{{ asset('img/logo.png') }}" alt="">VetLink</a>
        <a class="boton" href="{{ route('landing') }}">Volver</a>
    </header>
    <section class="land-hero">
        <div class="land-foto" role="img" aria-label="Vaca en el campo"><p class="lema">El cuidado de tu ganado, en un mismo lugar.</p></div>
        {{-- Solo cuentas existentes: no hay registro en la web. --}}
        <div class="land-texto">
            <div><h1>Bienvenido a VetLink</h1><p class="suave" style="margin-top:6px">Usá la misma cuenta que en la app.</p></div>
            @if (session('aviso'))<div class="aviso"><x-icono nombre="info" tam="18" />{{ session('aviso') }}</div>@endif
            @if ($errors->any())<div class="aviso error"><x-icono nombre="alerta" tam="18" />{{ $errors->first() }}</div>@endif
            <form method="post" action="{{ route('login.entrar') }}" novalidate>
                @csrf
                <label>Correo electrónico
                    <span class="campo"><x-icono nombre="perfil" tam="18" /><input type="email" name="correo" value="{{ old('correo') }}" maxlength="254" required autofocus autocomplete="email" placeholder="sofia@ejemplo.com"></span>
                </label>
                <label>Contraseña
                    <span class="campo"><x-icono nombre="candado" tam="18" /><input type="password" name="contrasena" maxlength="128" required autocomplete="current-password"></span>
                </label>
                <button class="boton">Iniciar sesión</button>
            </form>
            <div class="aviso"><x-icono nombre="info" tam="18" /><span>¿No tenés cuenta? Pedí la invitación al dueño de la granja desde la app.</span></div>
        </div>
    </section>
</div>
@endsection
