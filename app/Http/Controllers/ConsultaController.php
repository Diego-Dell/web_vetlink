<?php

namespace App\Http\Controllers;

use App\Services\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

// 1. Pantallas de consulta: Inicio, Gestionar ganado, Ficha del animal, Generar reportes y Perfil.
class ConsultaController extends Controller
{
    public function __construct(private Consulta $consulta) {}

    // 2. Cambiar de granja solo cambia qué se muestra.
    public function elegirGranja(Request $request, string $id)
    {
        abort_unless($this->consulta->granjas()->contains('id', $id), 404);
        $request->session()->put('granja', $id);

        return redirect()->route('inicio');
    }

    public function inicio()
    {
        $granja = $this->consulta->granjaActual();
        if (! $granja) {
            return view('consulta.sin-granja');
        }
        $fichas = $this->consulta->fichas($granja['id']);
        $activas = $fichas->whereNull('fecha_baja')->values();
        $tratamientos = $this->consulta->tratamientos($activas);
        $prevenciones = $this->consulta->prevenciones($activas)->where('estado', 'pendiente');

        return view('consulta.inicio', [
            'granja' => $granja,
            'activos' => $activas->count(),
            'enTratamiento' => $tratamientos->pluck('ficha_id')->unique()->count(),
            'pendientesPrevencion' => $prevenciones->count(),
            'tratamientos' => $tratamientos,
            'faltantes' => $tratamientos->sum(fn ($t) => $t['avance']['faltan'] ?? 0),
            'prevenciones' => $prevenciones->take(6),
            'nombres' => $fichas->keyBy('id'),
        ]);
    }

    public function ganado(Request $request)
    {
        $granja = $this->consulta->granjaActual();
        if (! $granja) {
            return view('consulta.sin-granja');
        }
        $filtros = $request->validate([
            'buscar' => ['nullable', 'string', 'max:60'],
            'estado' => ['nullable', 'in:activos,baja,todos'],
        ]);
        $buscar = mb_strtolower(trim($filtros['buscar'] ?? ''));
        $estado = $filtros['estado'] ?? 'activos';
        $fichas = $this->consulta->fichas($granja['id'])
            ->filter(fn ($f) => $estado === 'todos' || ($estado === 'baja') === ($f['fecha_baja'] !== null))
            ->filter(fn ($f) => $buscar === '' || str_contains(mb_strtolower($f['identificacion'].' '.($f['nombre'] ?? '')), $buscar))
            ->values();
        $tratamientos = $this->consulta->tratamientos($fichas)->groupBy('ficha_id');

        return view('consulta.ganado', compact('granja', 'fichas', 'tratamientos', 'buscar', 'estado'));
    }

    public function ficha(string $id)
    {
        $ficha = $this->consulta->ficha($id);
        abort_if(! $ficha, 404, 'La ficha no existe o no pertenece a tus granjas.');
        $una = collect([$ficha]);

        return view('consulta.ficha', [
            'granja' => $this->consulta->granjaActual(),
            'ficha' => $ficha,
            'tratamientos' => $this->consulta->tratamientos($una, false),
            'atenciones' => $this->consulta->atenciones($id),
            'aplicaciones' => $this->consulta->aplicaciones($id),
            'prevenciones' => $this->consulta->prevenciones($una),
        ]);
    }

    public function reportes(Request $request)
    {
        $granja = $this->consulta->granjaActual();
        if (! $granja) {
            return view('consulta.sin-granja');
        }
        $periodo = $request->validate(['periodo' => ['nullable', 'in:semana,mes,trimestre']])['periodo'] ?? 'mes';
        $desde = now()->subDays(['semana' => 7, 'mes' => 30, 'trimestre' => 90][$periodo])->toDateString();
        $fichas = $this->consulta->fichas($granja['id']);
        $tratamientos = $this->consulta->tratamientos($fichas, false)->filter(fn ($t) => $t['inicio'] >= $desde)->values();
        // Aplicaciones agrupadas por semana (lunes) para el gráfico.
        $semanas = [];
        for ($s = now()->subDays(['semana' => 7, 'mes' => 30, 'trimestre' => 90][$periodo])->startOfWeek(); $s <= now(); $s = $s->copy()->addWeek()) {
            $semanas[$s->toDateString()] = 0;
        }
        foreach ($this->consulta->aplicacionesDesde($fichas, $desde) as $a) {
            $lunes = Carbon::parse($a['fecha_hora_real'])->startOfWeek()->toDateString();
            if (isset($semanas[$lunes])) {
                $semanas[$lunes]++;
            }
        }

        return view('consulta.reportes', [
            'granja' => $granja,
            'periodo' => $periodo,
            'desde' => $desde,
            'semanas' => collect($semanas),
            'tratamientos' => $tratamientos,
            'aplicadas' => $tratamientos->sum(fn ($t) => $t['avance']['aplicadas']),
            'indicadas' => $tratamientos->sum(fn ($t) => $t['avance']['total'] ?? 0),
            'prevenciones' => $this->consulta->prevenciones($fichas)->filter(fn ($p) => $p['fecha_programada'] >= $desde)->values(),
        ]);
    }

    public function perfil()
    {
        return view('consulta.perfil', ['granja' => $this->consulta->granjaActual(), 'granjas' => $this->consulta->granjas()]);
    }
}
