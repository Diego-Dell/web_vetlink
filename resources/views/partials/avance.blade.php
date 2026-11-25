@if ($avance['total'])
    <div class="avance">
        <div class="fila-texto"><span>Número de aplicaciones: {{ $avance['aplicadas'] }}/{{ $avance['total'] }}</span><span>{{ $avance['faltan'] ? 'Faltan '.$avance['faltan'] : 'Completo' }}</span></div>
        <div class="pista"><div style="width: {{ max(4, $avance['porcentaje']) }}%"></div></div>
    </div>
@else
    <div class="suave chico">Número de aplicaciones: {{ $avance['aplicadas'] }}</div>
@endif
