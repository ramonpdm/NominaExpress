<?php

namespace App\Controllers\Frontend;

use Throwable;
use App\Config\Auth;
use App\Entities\Cargo;
use App\Entities\Departamento;

class CargosController extends BaseController
{
    public function index(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();

        return $this->renderView('admin/cargos', [
            'title' => 'Cargos',
            'cargos' => $em->getRepository(Cargo::class)->findBy(['activo' => true], ['nombre' => 'ASC']),
            'departamentos' => $em->getRepository(Departamento::class)->findBy(['activo' => true], ['nombre' => 'ASC']),
        ]);
    }

    public function guardar(): string
    {
        Auth::requireAdmin();

        $em = $this->orm->getProvider();

        try {
            $nombre = trim($_POST['nombre'] ?? '');
            $deptoId = (int) ($_POST['departamento_id'] ?? 0);

            if ($nombre === '') throw new \Exception('El nombre es obligatorio');
            $depto = $em->getRepository(Departamento::class)->find($deptoId);
            if (!$depto) throw new \Exception('Departamento inválido');

            $cargo = new Cargo([
                'nombre' => $nombre,
                'departamento' => $depto,
                'salario_base_sugerido' => number_format((float) ($_POST['salario_base_sugerido'] ?? 0), 2, '.', ''),
            ]);

            $em->persist($cargo);
            $em->flush();

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Cargo creado'];
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => $e->getMessage()];
        }

        return $this->redirect('/cargos');
    }
}
