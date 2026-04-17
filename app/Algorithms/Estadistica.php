<?php

namespace App\Algorithms;

use Closure;

/**
 * Algoritmos aritméticos — Unidad 5 de INF3220.
 *
 * Contiene implementaciones iterativas de:
 *   - Suma iterativa (acumulador)    — Θ(n)
 *   - Media aritmética               — Θ(n)
 *   - Mínimo y Máximo                — Θ(n)
 *
 * Todos recorren el arreglo una sola vez, por lo que son lineales
 * en el tamaño de la entrada.
 */
final class Estadistica
{
    /**
     * Suma Iterativa: recorre el arreglo acumulando los valores numéricos
     * devueltos por $extractor. Algoritmo base de los totales de nómina.
     *
     * Complejidad: Θ(n).
     */
    public static function suma(array $items, string|Closure $extractor): float
    {
        $get = self::buildGetter($extractor);
        $total = 0.0;

        foreach ($items as $item) {
            $total += (float) $get($item);
        }

        return $total;
    }

    /**
     * Media Aritmética: suma / cantidad. Devuelve 0.0 si el arreglo está vacío.
     *
     * Complejidad: Θ(n).
     */
    public static function media(array $items, string|Closure $extractor): float
    {
        $n = count($items);
        if ($n === 0) return 0.0;

        return self::suma($items, $extractor) / $n;
    }

    /**
     * Mínimo. Recorrido lineal manteniendo el menor visto.
     *
     * Complejidad: Θ(n).
     */
    public static function minimo(array $items, string|Closure $extractor): ?float
    {
        $get = self::buildGetter($extractor);
        $min = null;

        foreach ($items as $item) {
            $v = (float) $get($item);
            if ($min === null || $v < $min) {
                $min = $v;
            }
        }

        return $min;
    }

    /**
     * Máximo. Recorrido lineal manteniendo el mayor visto.
     *
     * Complejidad: Θ(n).
     */
    public static function maximo(array $items, string|Closure $extractor): ?float
    {
        $get = self::buildGetter($extractor);
        $max = null;

        foreach ($items as $item) {
            $v = (float) $get($item);
            if ($max === null || $v > $max) {
                $max = $v;
            }
        }

        return $max;
    }

    private static function buildGetter(string|Closure $extractor): Closure
    {
        if ($extractor instanceof Closure) {
            return $extractor;
        }

        return fn ($item) => is_array($item) ? ($item[$extractor] ?? 0) : ($item->{$extractor} ?? 0);
    }
}
