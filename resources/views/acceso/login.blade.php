@extends('layouts.base')
@section('titulo', 'Iniciar sesión')
@section('cuerpo')
<div class="movil landing acceso" style="padding-bottom:0">
    <section class="portada" style="height:260px">
        <div class="marca">
            <div><img src="{{ asset('img/logo.png') }}" alt="">VetLink</div>
            <a href="{{ route('landing') }}">Volver</a>
        </div>
    </section>
    <section class="hoja">
        <div><h1>Bienvenido a VetLink</h1><p class="suave" style="margin:6px 0 0">Usá la misma cuenta que en la app.</p></div>
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
        <p class="suave chico" style="margin:0">¿No tenés cuenta? Pedí la invitación al dueño de la granja desde la app. Para recuperar la contraseña usá “Olvidé mi contraseña” en la app.</p>
    </section>
</div>
@endsection
