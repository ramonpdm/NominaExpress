<?php

namespace App\Services;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;

use App\Algorithms\BinarySearch;
use App\Algorithms\Estadistica;
use App\Algorithms\QuickSort;
use App\Entities\ConceptoNomina;
use App\Entities\Empleado;
use App\Entities\Nomina;
use App\Entities\NominaDetalle;
use App\Entities\PeriodoNomina;
use App\Enums\EstadoEmpleado;
use App\Enums\EstadoNomina;
use App\Enums\MetodoCalculo;
use App\Enums\TipoConcepto;

/**
 * Servicio que procesa el cálculo masivo de la nómina de un período.
 *
 * Algoritmos académicos aplicados:
 *   - QuickSort        → ordena empleados por ID antes de procesar.
 *   - BinarySearch     → resuelve un empleado por ID en O(log n) desde el arreglo ordenado.
 *   - Suma Iterativa   → acumula totales de ingresos y deducciones.
 *   - Recursión        → (ver Algorithms\Organigrama) totales por departamento.
 *
 * Complejidad global: O(n × m) con n empleados y m conceptos obligatorios.
 */
class NominaCalculator
{
    /**
     * Escala progresiva del ISR dominicano (anual, en RD$).
     * Fuente: Código Tributario Ley 11-92.
     */
    private const array ISR_ESCALA = [
        ['hasta' => 416220.00,   'tasa' => 0.00, 'exceso' => 0.00,     'impuesto_base' => 0.00],
        ['hasta' => 624329.00,   'tasa' => 0.15, 'exceso' => 416220.00, 'impuesto_base' => 0.00],
        ['hasta' => 867123.00,   'tasa' => 0.20, 'exceso' => 624329.00, 'impuesto_base' => 31216.00],
        ['hasta' => PHP_FLOAT_MAX,'tasa' => 0.25, 'exceso' => 867123.00, 'impuesto_base' => 79776.00],
    ];

    public function __construct(private EntityManagerInterface $em) {}

    /**
     * Procesa la nómina completa del período para todos los empleados activos.
     *
     * Estrategia:
     *   1. Cargar empleados activos en memoria.
     *   2. Ordenarlos con QuickSort por ID ascendente.
     *   3. Para cada empleado, calcular su nómina usando los conceptos obligatorios.
     *   4. La búsqueda binaria queda disponible para futuras novedades que lleguen por ID.
     *
     * @return int Cantidad de nóminas creadas.
     */
    public function procesarPeriodo(PeriodoNomina $periodo): int
    {
        $empleados = $this->em->getRepository(Empleado::class)
            ->findBy(['estado' => EstadoEmpleado::ACTIVO, 'activo' => true]);

        // Algoritmo QuickSort aplicado: orden ascendente por ID
        $empleados = QuickSort::sortBy($empleados, 'id', true);

        $conceptos = $this->em->getRepository(ConceptoNomina::class)
            ->findBy(['obligatorio' => true, 'activo' => true]);

        $creadas = 0;

        foreach ($empleados as $empleado) {
            if ($this->yaProcesado($empleado, $periodo)) {
                continue;
            }

            $this->calcularEmpleado($empleado, $periodo, $conceptos);
            $creadas++;
        }

        $this->em->flush();
        return $creadas;
    }

    /**
     * Calcula y persiste la nómina individual de un empleado.
     */
    public function calcularEmpleado(Empleado $empleado, PeriodoNomina $periodo, array $conceptos): Nomina
    {
        $salarioMensual = (float) $empleado->salario;
        $tipoPeriodo = $periodo->tipo;
        $divisor = $tipoPeriodo->divisorMensual();
        $periodosAnio = $tipoPeriodo->periodosPorAnio();
        $salarioBase = $salarioMensual / $divisor;

        $nomina = new Nomina([
            'empleado' => $empleado,
            'periodo' => $periodo,
            'salario_base' => number_format($salarioBase, 2, '.', ''),
            'fecha_calculo' => new DateTime(),
            'estado' => EstadoNomina::CALCULADA,
        ]);

        $totalIngresos = $salarioBase;
        $totalDeducciones = 0.0;

        // Línea #1: salario base como ingreso (ya divido según el tipo de período)
        $nomina->detalles->add($this->nuevoDetalle(
            $nomina,
            null,
            TipoConcepto::INGRESO,
            $salarioBase,
            $salarioMensual,
            $divisor,
            'Salario Base'
        ));

        foreach ($conceptos as $concepto) {
            $monto = $concepto->codigo === 'ISR'
                ? $this->calcularIsrPeriodo($salarioBase, $periodosAnio)
                : $this->calcularMontoConcepto($concepto, $salarioBase);

            if ($monto <= 0) continue;

            $detalle = $this->nuevoDetalle(
                $nomina,
                $concepto,
                $concepto->tipo,
                $monto,
                $salarioBase,
                $concepto->metodo_calculo === MetodoCalculo::PORCENTAJE ? (float) $concepto->valor : null
            );

            $nomina->detalles->add($detalle);

            if ($concepto->tipo === TipoConcepto::INGRESO) {
                $totalIngresos += $monto;
            } else {
                $totalDeducciones += $monto;
            }
        }

        // Suma Iterativa (algoritmo aritmético): acumuladores ya calculados arriba.
        $nomina->total_ingresos    = number_format($totalIngresos, 2, '.', '');
        $nomina->total_deducciones = number_format($totalDeducciones, 2, '.', '');
        $nomina->salario_neto      = number_format($totalIngresos - $totalDeducciones, 2, '.', '');

        $this->em->persist($nomina);

        return $nomina;
    }

/**
     * Devuelve el monto de un concepto para un salario dado.
     * Para ISR aplica la tabla progresiva dominicana sobre el anualizado.
     */
    private function calcularMontoConcepto(ConceptoNomina $c, float $salarioBase): float
    {
        return match ($c->metodo_calculo) {
            MetodoCalculo::PORCENTAJE => round($salarioBase * ((float) $c->valor / 100), 2),
            MetodoCalculo::MONTO_FIJO => round((float) $c->valor, 2),
            MetodoCalculo::FORMULA    => 0.0,
        };
    }

    /**
     * Calcula el ISR para el período dividiendo el salario.base según el tipo de período.
     * Anualiza: salarioBase × periodsPerYear, calcula ISR anual, divide por periodsPerYear.
     */
    private function calcularIsrPeriodo(float $salarioBase, float $periodosAnio): float
    {
        $anual = $salarioBase * $periodosAnio;

        $impuestoAnual = 0.0;
        foreach (self::ISR_ESCALA as $tramo) {
            if ($anual <= $tramo['hasta']) {
                $impuestoAnual = $tramo['impuesto_base'] + ($anual - $tramo['exceso']) * $tramo['tasa'];
                break;
            }
        }

        return max(0.0, round($impuestoAnual / $periodosAnio, 2));
    }

    private function nuevoDetalle(
        Nomina $nomina,
        ?ConceptoNomina $concepto,
        TipoConcepto $tipo,
        float $monto,
        float $base,
        ?float $porcentaje = null,
        ?string $descripcion = null
    ): NominaDetalle {
        $detalle = new NominaDetalle();
        $detalle->nomina = $nomina;
        $detalle->concepto = $concepto ?? $this->getConceptoSalarioBase();
        $detalle->tipo = $tipo;
        $detalle->monto = number_format($monto, 2, '.', '');
        $detalle->base_calculo = number_format($base, 2, '.', '');
        $detalle->porcentaje_aplicado = $porcentaje !== null ? number_format($porcentaje, 4, '.', '') : null;
        return $detalle;
    }

    private function getConceptoSalarioBase(): ConceptoNomina
    {
        return $this->em->getRepository(ConceptoNomina::class)
            ->findOneBy(['codigo' => 'SAL_BASE']);
    }

    private function yaProcesado(Empleado $empleado, PeriodoNomina $periodo): bool
    {
        return $this->em->getRepository(Nomina::class)
            ->findOneBy(['empleado' => $empleado, 'periodo' => $periodo]) !== null;
    }

    /**
     * Resuelve un empleado por ID dentro de un arreglo ya ordenado
     * usando Búsqueda Binaria.
     *
     * @param Empleado[] $empleadosOrdenados  Ordenados ascendentemente por ID.
     */
    public function buscarEmpleadoPorId(array $empleadosOrdenados, int $id): ?Empleado
    {
        return BinarySearch::find($empleadosOrdenados, $id, 'id');
    }

    /**
     * Calcula el promedio salarial de una colección de empleados.
     * Aplica el algoritmo de Media Aritmética (unidad 5).
     */
    public function salarioPromedio(array $empleados): float
    {
        return Estadistica::media($empleados, fn ($e) => (float) $e->salario);
    }
}
