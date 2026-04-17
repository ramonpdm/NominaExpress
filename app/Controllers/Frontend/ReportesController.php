<?php

namespace App\Controllers\Frontend;

use App\Algorithms\Estadistica;
use App\Algorithms\Organigrama;
use App\Algorithms\QuickSort;
use App\Config\Auth;
use App\Entities\Departamento;
use App\Entities\Empleado;
use App\Entities\Nomina;
use App\Entities\PeriodoNomina;
use App\Enums\EstadoEmpleado;

class ReportesController extends BaseController
{
    /**
     * Reporte de nómina por período.
     * Se apoya en Suma Iterativa (Estadistica::suma) para los totales
     * al pie de tabla, y QuickSort para ordenar por salario neto.
     */
    public function nomina(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $periodos = $em->getRepository(PeriodoNomina::class)->findBy([], ['fecha_inicio' => 'DESC']);

        $periodoId = isset($_GET['periodo_id']) ? (int) $_GET['periodo_id'] : ($periodos[0]->id ?? null);
        $periodo = $periodoId ? $em->getRepository(PeriodoNomina::class)->find($periodoId) : null;

        $nominas = $periodo
            ? $em->getRepository(Nomina::class)->findBy(['periodo' => $periodo])
            : [];

        $nominas = QuickSort::sortBy($nominas, 'salario_neto', ascendente: false);

        $totales = [
            'base'        => Estadistica::suma($nominas, 'salario_base'),
            'ingresos'    => Estadistica::suma($nominas, 'total_ingresos'),
            'deducciones' => Estadistica::suma($nominas, 'total_deducciones'),
            'neto'        => Estadistica::suma($nominas, 'salario_neto'),
        ];

        return $this->renderView('reportes/nomina', [
            'title' => 'Reporte de Nómina',
            'periodos' => $periodos,
            'periodo' => $periodo,
            'nominas' => $nominas,
            'totales' => $totales,
        ]);
    }

    /**
     * Planilla de empleados filtrable. Usa QuickSort para el orden final.
     */
    public function empleados(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $criteria = ['activo' => true];

        if (!empty($_GET['estado'])) {
            $criteria['estado'] = EstadoEmpleado::from($_GET['estado']);
        }
        if (!empty($_GET['departamento_id'])) {
            $criteria['departamento'] = $em->getRepository(Departamento::class)->find((int) $_GET['departamento_id']);
        }

        $empleados = $em->getRepository(Empleado::class)->findBy($criteria);

        $orden = $_GET['orden'] ?? 'nombres';
        $empleados = QuickSort::sortBy($empleados, $orden, ascendente: $orden !== 'salario');

        $promedio = Estadistica::media($empleados, fn ($e) => (float) $e->salario);
        $minimo   = Estadistica::minimo($empleados, fn ($e) => (float) $e->salario);
        $maximo   = Estadistica::maximo($empleados, fn ($e) => (float) $e->salario);

        return $this->renderView('reportes/empleados', [
            'title' => 'Planilla de Empleados',
            'empleados' => $empleados,
            'departamentos' => $em->getRepository(Departamento::class)->findBy(['activo' => true]),
            'estadisticas' => [
                'total' => count($empleados),
                'promedio' => $promedio,
                'minimo' => $minimo ?? 0,
                'maximo' => $maximo ?? 0,
            ],
        ]);
    }

    /**
     * Reporte por departamento: usa el algoritmo recursivo de Organigrama
     * para calcular costos totales incluyendo subdepartamentos.
     */
    public function departamentos(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $raices = $em->getRepository(Departamento::class)
            ->findBy(['padre' => null, 'activo' => true], ['nombre' => 'ASC']);

        $filas = [];
        foreach ($raices as $depto) {
            $filas[] = [
                'depto' => $depto,
                'empleados' => Organigrama::contarEmpleados($depto),
                'costo_total' => Organigrama::costoNominaTotal($depto),
            ];
        }

        return $this->renderView('reportes/departamentos', [
            'title' => 'Reporte por Departamento',
            'filas' => $filas,
        ]);
    }
}
