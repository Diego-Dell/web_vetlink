<a class="tarjeta" href="{{ route('ficha', $t['ficha_id']) }}">
    <div class="fila">
        <span class="burbuja"><x-icono nombre="medicamento" /></span>
        <div class="textos"><strong>{{ $t['medicamento'] }}</strong><span class="suave chico">{{ $t['animal'] }} · {{ \App\Support\Dosis::numero($t['dosis']) }} {{ $t['unidad'] }} cada {{ $t['frecuencia'] }}</span></div>
    </div>
    @include('partials.avance', ['avance' => $t['avance']])
</a>
