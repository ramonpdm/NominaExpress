<?php

namespace App\Controllers\Frontend;

use DateTime;
use Throwable;

use App\Config\Auth;
use App\Entities\PeriodoNomina;
use App\Enums\EstadoPeriodo;
use App\Enums\TipoPeriodo;

class PeriodosController extends BaseController
{
    public function index(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $periodos = $em->getRepository(PeriodoNomina::class)
            ->findBy(['activo' => true], ['fecha_inicio' => 'DESC']);

        return $this->renderView('nomina/periodos', [
            'title' => 'Períodos de Nómina',
            'periodos' => $periodos,
        ]);
    }

    public function guardar(): string
    {
        Auth::requireEditor();

        $em = $this->orm->getProvider();

        try {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') throw new \Exception('El nombre es obligatorio');

            $periodo = new PeriodoNomina([
                'nombre' => $nombre,
                'tipo' => TipoPeriodo::from($_POST['tipo']),
                'fecha_inicio' => new DateTime($_POST['fecha_inicio']),
                'fecha_fin' => new DateTime($_POST['fecha_fin']),
                'fecha_pago' => new DateTime($_POST['fecha_pago']),
                'estado' => EstadoPeriodo::ABIERTO,
            ]);

            $em->persist($periodo);
            $em->flush();

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Período creado'];
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => $e->getMessage()];
        }

        return $this->redirect('/periodos');
    }

    public function cerrar(int $id): string
    {
        Auth::requireEditor();

        $em = $this->orm->getProvider();
        $periodo = $em->getRepository(PeriodoNomina::class)->find($id);

        if ($periodo) {
            $empleadosActivos = $em->getRepository(\App\Entities\Empleado::class)
                ->count(['estado' => \App\Enums\EstadoEmpleado::ACTIVO, 'activo' => true]);
            
            $procesados = $periodo->nominas->count();

            if ($procesados < $empleadosActivos) {
                $faltantes = $empleadosActivos - $procesados;
                $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => "No se puede cerrar/pagar el período. Faltan $faltantes empleados por procesar."];
            } else {
                $periodo->estado = EstadoPeriodo::PAGADO;
                $em->flush();
                $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Período cerrado y marcado como PAGADO exitosamente.'];
            }
        }

        return $this->redirect('/periodos');
    }
}
