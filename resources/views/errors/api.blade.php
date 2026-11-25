@extends('layouts.base')
@section('titulo', 'Sin conexión')
@section('cuerpo')
<div class="movil" style="display:grid;place-items:center;padding:16px">
    <div class="tarjeta" style="text-align:center;display:grid;gap:12px">
        <h1>No se pudo conectar</h1>
        <p class="suave" style="margin:0">Revisá tu internet y volvé a intentar.</p>
        <a class="boton" href="{{ url()->current() }}">Reintentar</a>
    </div>
</div>
@endsection
