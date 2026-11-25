<div class="tarjeta kpi">
    <div class="fila"><span class="burbuja chica"><x-icono :nombre="$icono" tam="16" /></span>{{ $nombre }}</div>
    <div class="cifra">{{ $valor }}</div>
    @isset($detalle)<div class="suave chico">{{ $detalle }}</div>@endisset
</div>
