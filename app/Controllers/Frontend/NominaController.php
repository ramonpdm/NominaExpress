<?php

namespace App\Controllers\Frontend;

use Throwable;

use App\Algorithms\Estadistica;
use App\Algorithms\QuickSort;
use App\Config\Auth;
use App\Entities\Empleado;
use App\Entities\Nomina;
use App\Entities\PeriodoNomina;
use App\Enums\EstadoEmpleado;
use App\Services\NominaCalculator;

class NominaController extends BaseController
{
    public function procesar(int $periodoId): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $periodo = $em->getRepository(PeriodoNomina::class)->find($periodoId);
        if (!$periodo) return $this->renderView(404);

        $empleados = $em->getRepository(Empleado::class)
            ->findBy(['estado' => EstadoEmpleado::ACTIVO, 'activo' => true]);

        // Orden por nombre usando QuickSort para mostrar la tabla ordenada
        $empleados = QuickSort::sortBy($empleados, 'nombres', true);

        $procesados = [];
        foreach ($periodo->nominas as $n) {
            $procesados[$n->empleado->id] = $n;
        }

        $totalBase       = Estadistica::suma($periodo->nominas->toArray(), 'salario_base');
        $totalDeduc      = Estadistica::suma($periodo->nominas->toArray(), 'total_deducciones');
        $totalNeto       = Estadistica::suma($periodo->nominas->toArray(), 'salario_neto');

        return $this->renderView('nomina/procesar', [
            'title' => 'Procesar: ' . $periodo->nombre,
            'periodo' => $periodo,
            'empleados' => $empleados,
            'procesados' => $procesados,
            'totales' => [
                'base' => $totalBase,
                'deducciones' => $totalDeduc,
                'neto' => $totalNeto,
                'procesados' => count($procesados),
                'total' => count($empleados),
            ],
        ]);
    }

    public function ejecutar(int $periodoId): string
    {
        Auth::requireEditor();

        $em = $this->orm->getProvider();
        $periodo = $em->getRepository(PeriodoNomina::class)->find($periodoId);

        if (!$periodo) return $this->renderView(404);

        try {
            $calc = new NominaCalculator($em);
            $creadas = $calc->procesarPeriodo($periodo);

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => "Se calcularon $creadas nóminas."];
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => 'Error al procesar: ' . $e->getMessage()];
        }

        return $this->redirect('/nomina/procesar/' . $periodoId);
    }

    public function comprobante(int $nominaId): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $nomina = $em->getRepository(Nomina::class)->find($nominaId);

        if (!$nomina) return $this->renderView(404);

        return $this->renderView('nomina/comprobante', [
            'title' => 'Comprobante — ' . $nomina->empleado->getNombreCompleto(),
            'nomina' => $nomina,
        ]);
    }
}
