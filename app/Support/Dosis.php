<?php

namespace App\Support;

// 1. Avance de un tratamiento: dosis aplicadas, total y faltantes.
class Dosis
{
    // 2. Total a partir de "Cada" y "Duración" (por ejemplo "24 h" y "5 días"). Null si no se puede calcular.
    public static function total(?string $frecuencia, ?string $duracion): ?int
    {
        $cada = self::horas($frecuencia);
        $dura = self::horas($duracion, 24);
        if ($frecuencia !== null && preg_match('/única|unica/i', $frecuencia)) {
            return 1;
        }
        if (! $cada || ! $dura) {
            return null;
        }

        return max(1, (int) ceil($dura / $cada));
    }

    // 3. Dosis sin ceros de más: 5 → "5", 2.5 → "2,5".
    public static function numero(float|int|string|null $valor): string
    {
        return $valor === null ? '' : rtrim(rtrim(number_format((float) $valor, 3, ',', ''), '0'), ',');
    }

    public static function avance(int $aplicadas, ?int $total): array
    {
        $faltan = $total === null ? null : max(0, $total - $aplicadas);

        return [
            'aplicadas' => $aplicadas,
            'total' => $total,
            'faltan' => $faltan,
            'porcentaje' => $total ? min(100, (int) round($aplicadas * 100 / $total)) : 0,
        ];
    }

    private static function horas(?string $texto, int $porDefecto = 1): ?float
    {
        if ($texto === null || ! preg_match('/(\d+(?:[.,]\d+)?)\s*([a-záéíóú]*)/iu', $texto, $m)) {
            return null;
        }
        $n = (float) str_replace(',', '.', $m[1]);
        $u = mb_strtolower($m[2]);

        return match (true) {
            str_starts_with($u, 'h') => $n,
            str_starts_with($u, 'd') => $n * 24,
            str_starts_with($u, 's') => $n * 24 * 7,
            default => $n * $porDefecto,
        };
    }
}
