<?php

namespace App\Services;

use App\Support\Dosis;
use Illuminate\Support\Collection;

// 1. Datos de la granja elegida leídos de Supabase; las políticas RLS deciden qué ve cada cuenta.
class Consulta
{
    public function __construct(private Supabase $supabase) {}

    private function leer(string $tabla, array $filtros = []): Collection
    {
        return collect($this->supabase->leer(session('vetlink.token'), $tabla, $filtros));
    }

    private static function en(Collection $ids): string
    {
        return 'in.('.$ids->implode(',').')';
    }

    // 2. Granjas confirmadas de la cuenta con su rol; el administrador ve las que RLS le permite.
    public function granjas(): Collection
    {
        $pedido = request()->attributes;
        if (! $pedido->has('vetlink.granjas')) {
            $granjas = $this->leer('vinculaciones', [
                'select' => 'rol,granjas(id,nombre,confirmada)',
                'cuenta_id' => 'eq.'.session('vetlink.cuenta'),
                'activa' => 'eq.true',
            ])->filter(fn ($v) => $v['granjas']['confirmada'] ?? false)
                ->map(fn ($v) => $v['granjas'] + ['rol' => $v['rol'] === 'dueno' ? 'dueño' : $v['rol']]);
            if ($granjas->isEmpty()) {
                $granjas = $this->leer('granjas', ['select' => 'id,nombre,confirmada', 'confirmada' => 'eq.true'])
                    ->map(fn ($g) => $g + ['rol' => 'administrador']);
            }
            $pedido->set('vetlink.granjas', $granjas->sortBy('nombre')->values());
        }

        return $pedido->get('vetlink.granjas');
    }

    public function granjaActual(): ?array
    {
        $granjas = $this->granjas();

        return $granjas->firstWhere('id', session('granja')) ?? $granjas->first();
    }

    public function fichas(string $granjaId): Collection
    {
        return $this->leer('fichas_bovinas', ['granja_id' => 'eq.'.$granjaId, 'order' => 'identificacion']);
    }

    public function ficha(string $id): ?array
    {
        return $this->leer('fichas_bovinas', ['id' => 'eq.'.$id])->first();
    }

    // 3. Tratamientos con su avance: aplicaciones vigentes sobre el total de dosis.
    public function tratamientos(Collection $fichas, bool $soloVigentes = true): Collection
    {
        if ($fichas->isEmpty()) {
            return collect();
        }
        $ids = $fichas->pluck('id');
        $filtros = ['ficha_id' => self::en($ids), 'anulado' => 'eq.false', 'order' => 'inicio.desc'];
        if ($soloVigentes) {
            $filtros['fecha_fin'] = 'is.null';
        }
        $tratamientos = $this->leer('tratamientos', $filtros);
        if ($tratamientos->isEmpty()) {
            return $tratamientos;
        }
        $aplicadas = $this->leer('aplicaciones', [
            'select' => 'tratamiento_id',
            'tratamiento_id' => self::en($tratamientos->pluck('id')),
            'anulado' => 'eq.false',
        ])->countBy('tratamiento_id');
        $nombres = $fichas->keyBy('id');

        return $tratamientos->map(function ($t) use ($aplicadas, $nombres) {
            $ficha = $nombres[$t['ficha_id']] ?? [];
            $t['animal'] = trim(($ficha['identificacion'] ?? '').' · '.($ficha['nombre'] ?? ''), ' ·');
            $t['avance'] = Dosis::avance($aplicadas[$t['id']] ?? 0, Dosis::total($t['frecuencia'], $t['duracion']));

            return $t;
        })->values();
    }

    public function prevenciones(Collection $fichas): Collection
    {
        if ($fichas->isEmpty()) {
            return collect();
        }

        return $this->leer('prevenciones', ['ficha_id' => self::en($fichas->pluck('id')), 'order' => 'fecha_programada']);
    }

    // 4. Fechas de aplicación desde una fecha, para el gráfico por semana.
    public function aplicacionesDesde(Collection $fichas, string $desde): Collection
    {
        if ($fichas->isEmpty()) {
            return collect();
        }

        return $this->leer('aplicaciones', [
            'select' => 'fecha_hora_real',
            'ficha_id' => self::en($fichas->pluck('id')),
            'anulado' => 'eq.false',
            'fecha_hora_real' => 'gte.'.$desde,
        ]);
    }

    public function atenciones(string $fichaId): Collection
    {
        return $this->leer('atenciones_clinicas', ['ficha_id' => 'eq.'.$fichaId, 'anulado' => 'eq.false', 'order' => 'fecha.desc']);
    }

    public function aplicaciones(string $fichaId): Collection
    {
        return $this->leer('aplicaciones', ['ficha_id' => 'eq.'.$fichaId, 'anulado' => 'eq.false', 'order' => 'fecha_hora_real.desc']);
    }
}
