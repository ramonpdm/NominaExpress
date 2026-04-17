<?php

namespace App\Controllers\Frontend;

use Throwable;
use App\Config\Auth;
use App\Entities\Departamento;

class DepartamentosController extends BaseController
{
    public function index(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $deptos = $em->getRepository(Departamento::class)->findBy(['activo' => true], ['nombre' => 'ASC']);

        return $this->renderView('admin/departamentos', [
            'title' => 'Departamentos',
            'departamentos' => $deptos,
        ]);
    }

    public function guardar(): string
    {
        Auth::requireAdmin();

        $em = $this->orm->getProvider();

        try {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') throw new \Exception('El nombre es obligatorio');

            $padreId = (int) ($_POST['padre_id'] ?? 0);
            $padre = $padreId > 0 ? $em->getRepository(Departamento::class)->find($padreId) : null;

            $depto = new Departamento([
                'nombre' => $nombre,
                'descripcion' => $_POST['descripcion'] ?? null,
                'padre' => $padre,
            ]);

            $em->persist($depto);
            $em->flush();

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Departamento creado'];
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => $e->getMessage()];
        }

        return $this->redirect('/departamentos');
    }
}
