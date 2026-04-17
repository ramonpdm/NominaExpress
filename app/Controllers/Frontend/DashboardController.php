<?php

namespace App\Controllers\Frontend;

use App\Algorithms\Estadistica;
use App\Config\Auth;
use App\Entities\Empleado;
use App\Entities\Nomina;
use App\Entities\PeriodoNomina;
use App\Enums\EstadoEmpleado;
use App\Enums\EstadoPeriodo;

class DashboardController extends BaseController
{
    public function index(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();

        $empleadosActivos = $em->getRepository(Empleado::class)
            ->count(['estado' => EstadoEmpleado::ACTIVO, 'activo' => true]);

        $totalPeriodos = $em->getRepository(PeriodoNomina::class)->count([]);
        $periodosAbiertos = $em->getRepository(PeriodoNomina::class)
            ->count(['estado' => EstadoPeriodo::ABIERTO]);

        $nominas = $em->getRepository(Nomina::class)->findAll();
        $totalPagado = Estadistica::suma($nominas, 'salario_neto');

        $ultimosEmpleados = $em->getRepository(Empleado::class)
            ->findBy([], ['id' => 'DESC'], 5);

        $ultimosPeriodos = $em->getRepository(PeriodoNomina::class)
            ->findBy([], ['id' => 'DESC'], 5);

        return $this->renderView('dashboard/index', [
            'title' => 'Dashboard',
            'kpis' => [
                'empleados' => $empleadosActivos,
                'periodos' => $totalPeriodos,
                'abiertos' => $periodosAbiertos,
                'pagado' => $totalPagado,
            ],
            'ultimosEmpleados' => $ultimosEmpleados,
            'ultimosPeriodos' => $ultimosPeriodos,
        ]);
    }
}
