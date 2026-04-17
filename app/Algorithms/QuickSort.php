<?php

namespace App\Algorithms;

use Closure;

/**
 * QuickSort (Ordenación Rápida) — Unidad 4 de INF3220.
 *
 * Algoritmo "Divide y Vencerás":
 *   1. Elige un pivote.
 *   2. Particiona el arreglo: elementos < pivote a la izquierda, > pivote a la derecha.
 *   3. Aplica recursión sobre cada mitad.
 *
 * Complejidad:
 *   - Mejor caso:     Θ(n log n) — particiones balanceadas.
 *   - Caso promedio:  Θ(n log n).
 *   - Peor caso:      O(n²)      — pivote mal elegido (arreglo ya ordenado).
 *
 * Esta implementación usa el pivote del extremo derecho (esquema Lomuto)
 * por simplicidad pedagógica. En producción se aleatoriza para evitar
 * el peor caso.
 */
final class QuickSort
{
    /**
     * Ordena $items usando una función de comparación que devuelve un número
     * negativo, cero o positivo (igual que PHP usort, pero implementado a mano).
     *
     * @template T
     * @param array<int,T> $items
     * @param Closure(T,T):int $comparator
     * @return array<int,T>
     */
    public static function sort(array $items, Closure $comparator): array
    {
        $arr = array_values($items);
        self::quicksort($arr, 0, count($arr) - 1, $comparator);
        return $arr;
    }

    /**
     * Atajo para ordenar un arreglo de objetos/arrays por una propiedad/clave.
     * Si $extractor es un string, se trata como nombre de propiedad pública.
     *
     * @param array $items
     * @param string|Closure $extractor
     * @param bool $ascendente
     * @return array
     */
    public static function sortBy(array $items, string|Closure $extractor, bool $ascendente = true): array
    {
        $get = is_string($extractor)
            ? fn ($item) => is_array($item) ? ($item[$extractor] ?? null) : ($item->{$extractor} ?? null)
            : $extractor;

        $comparator = function ($a, $b) use ($get, $ascendente) {
            $va = $get($a);
            $vb = $get($b);

            if (is_numeric($va) && is_numeric($vb)) {
                $cmp = ($va <=> $vb);
            } else {
                $cmp = strcmp((string) $va, (string) $vb);
            }

            return $ascendente ? $cmp : -$cmp;
        };

        return self::sort($items, $comparator);
    }

    private static function quicksort(array &$arr, int $low, int $high, Closure $cmp): void
    {
        if ($low < $high) {
            $p = self::partition($arr, $low, $high, $cmp);
            self::quicksort($arr, $low, $p - 1, $cmp);
            self::quicksort($arr, $p + 1, $high, $cmp);
        }
    }

    private static function partition(array &$arr, int $low, int $high, Closure $cmp): int
    {
        $pivot = $arr[$high];
        $i = $low - 1;

        for ($j = $low; $j < $high; $j++) {
            if ($cmp($arr[$j], $pivot) <= 0) {
                $i++;
                [$arr[$i], $arr[$j]] = [$arr[$j], $arr[$i]];
            }
        }

        [$arr[$i + 1], $arr[$high]] = [$arr[$high], $arr[$i + 1]];
        return $i + 1;
    }
}
