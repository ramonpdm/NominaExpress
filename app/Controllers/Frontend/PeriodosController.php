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
            $periodo->estado = EstadoPeriodo::CERRADO;
            $em->flush();
            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Período cerrado'];
        }

        return $this->redirect('/periodos');
    }
}
