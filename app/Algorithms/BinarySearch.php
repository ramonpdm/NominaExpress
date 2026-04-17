<?php

namespace App\Algorithms;

use Closure;

/**
 * Búsqueda Binaria — Unidad 3 de INF3220.
 *
 * Dado un arreglo PREVIAMENTE ORDENADO, localiza un elemento en
 * tiempo logarítmico dividiendo repetidamente el espacio de búsqueda
 * por la mitad.
 *
 * Complejidad:
 *   - Mejor caso:    Ω(1)     — elemento en el medio en la primera iteración.
 *   - Caso promedio: Θ(log n).
 *   - Peor caso:     O(log n).
 *
 * Requisito: el arreglo DEBE estar ordenado según la clave de búsqueda.
 * Si no lo está, primero llamar a QuickSort::sortBy().
 */
final class BinarySearch
{
    /**
     * Busca un valor en un arreglo ordenado usando un extractor de clave.
     *
     * @param array $sortedItems     Arreglo ordenado ascendentemente.
     * @param mixed $needle          Valor a buscar.
     * @param string|Closure $keyExtractor   Propiedad o closure que devuelve la clave.
     * @return mixed|null            Elemento encontrado o null.
     */
    public static function find(array $sortedItems, mixed $needle, string|Closure $keyExtractor): mixed
    {
        $get = is_string($keyExtractor)
            ? fn ($item) => is_array($item) ? ($item[$keyExtractor] ?? null) : ($item->{$keyExtractor} ?? null)
            : $keyExtractor;

        $arr = array_values($sortedItems);
        $low = 0;
        $high = count($arr) - 1;

        while ($low <= $high) {
            $mid = intdiv($low + $high, 2);
            $midKey = $get($arr[$mid]);

            $cmp = self::compare($midKey, $needle);

            if ($cmp === 0) {
                return $arr[$mid];
            }

            if ($cmp < 0) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }

        return null;
    }

    /**
     * Devuelve el índice donde debería insertarse $needle para mantener el orden.
     * Útil cuando no queremos el elemento sino su posición.
     */
    public static function findIndex(array $sortedItems, mixed $needle, string|Closure $keyExtractor): int
    {
        $get = is_string($keyExtractor)
            ? fn ($item) => is_array($item) ? ($item[$keyExtractor] ?? null) : ($item->{$keyExtractor} ?? null)
            : $keyExtractor;

        $arr = array_values($sortedItems);
        $low = 0;
        $high = count($arr) - 1;

        while ($low <= $high) {
            $mid = intdiv($low + $high, 2);
            $cmp = self::compare($get($arr[$mid]), $needle);

            if ($cmp === 0) return $mid;
            if ($cmp < 0) $low = $mid + 1;
            else          $high = $mid - 1;
        }

        return -1;
    }

    private static function compare(mixed $a, mixed $b): int
    {
        if (is_numeric($a) && is_numeric($b)) {
            return $a <=> $b;
        }
        return strcmp((string) $a, (string) $b);
    }
}
