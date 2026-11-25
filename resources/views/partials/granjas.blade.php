<details class="granjas {{ $clase }}">
    <summary><x-icono nombre="granja" tam="14" />{{ $granja['nombre'] }} <span class="rol">· {{ ucfirst($granja['rol'] ?? '') }}</span> ▾</summary>
    <ul>
        @foreach ($misGranjas as $g)
            <li><a class="{{ $g['id'] === $granja['id'] ? 'activa' : '' }}" href="{{ route('granja', $g['id']) }}">{{ $g['nombre'] }} <span class="suave chico">· {{ ucfirst($g['rol']) }}</span></a></li>
        @endforeach
    </ul>
</details>
