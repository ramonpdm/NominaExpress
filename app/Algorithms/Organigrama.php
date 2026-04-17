<?php

namespace App\Algorithms;

use App\Entities\Departamento;

/**
 * Recursión sobre estructura jerárquica — Unidad 2 de INF3220.
 *
 * El organigrama de TechSoft RD es un árbol donde cada Departamento
 * puede tener subdepartamentos (autoreferencia padre/hijos). Este
 * algoritmo baja recursivamente sumando el costo total de nómina
 * y contando empleados a través de todo el subárbol.
 *
 * Demuestra el patrón clásico de recursión:
 *   f(nodo) = valor(nodo) + Σ f(hijo) para cada hijo
 *
 * Complejidad: Θ(n) donde n = nodos del subárbol.
 */
final class Organigrama
{
    /**
     * Suma recursivamente los salarios de todos los empleados activos
     * de un departamento y sus subdepartamentos.
     *
     * Caso base: departamento sin subdepartamentos → sólo suma sus empleados.
     * Caso recursivo: suma sus empleados + resultado recursivo por cada hijo.
     */
    public static function costoNominaTotal(Departamento $depto): float
    {
        $total = 0.0;

        foreach ($depto->empleados as $emp) {
            $total += (float) $emp->salario;
        }

        foreach ($depto->subdepartamentos as $sub) {
            $total += self::costoNominaTotal($sub);
        }

        return $total;
    }

    /**
     * Cuenta recursivamente la cantidad de empleados de un departamento
     * y todos sus subdepartamentos.
     */
    public static function contarEmpleados(Departamento $depto): int
    {
        $total = count($depto->empleados);

        foreach ($depto->subdepartamentos as $sub) {
            $total += self::contarEmpleados($sub);
        }

        return $total;
    }

    /**
     * Devuelve la profundidad del nodo en el árbol (raíz = 0).
     * Ejemplo de recursión ascendente usando el padre.
     */
    public static function profundidad(Departamento $depto): int
    {
        if ($depto->padre === null) {
            return 0;
        }
        return 1 + self::profundidad($depto->padre);
    }
}
