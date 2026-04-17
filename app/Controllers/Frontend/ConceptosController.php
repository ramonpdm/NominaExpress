<?php

namespace App\Controllers\Frontend;

use Throwable;
use App\Config\Auth;
use App\Entities\ConceptoNomina;
use App\Enums\MetodoCalculo;
use App\Enums\TipoConcepto;

class ConceptosController extends BaseController
{
    public function index(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();

        return $this->renderView('admin/conceptos', [
            'title' => 'Conceptos de Nómina',
            'conceptos' => $em->getRepository(ConceptoNomina::class)->findBy(['activo' => true], ['codigo' => 'ASC']),
        ]);
    }

    public function guardar(): string
    {
        Auth::requireAdmin();

        $em = $this->orm->getProvider();

        try {
            $codigo = strtoupper(trim($_POST['codigo'] ?? ''));
            $nombre = trim($_POST['nombre'] ?? '');

            if ($codigo === '' || $nombre === '') {
                throw new \Exception('Código y nombre son obligatorios');
            }

            if ($em->getRepository(ConceptoNomina::class)->findOneBy(['codigo' => $codigo])) {
                throw new \Exception("Ya existe un concepto con el código $codigo");
            }

            $c = new ConceptoNomina([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'tipo' => TipoConcepto::from($_POST['tipo']),
                'metodo_calculo' => MetodoCalculo::from($_POST['metodo_calculo']),
                'valor' => number_format((float) $_POST['valor'], 4, '.', ''),
                'obligatorio' => isset($_POST['obligatorio']),
            ]);

            $em->persist($c);
            $em->flush();

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Concepto creado'];
        } catch (Throwable $e) {
            $_SESSION['flash'] = ['tipo' => 'danger', 'msg' => $e->getMessage()];
        }

        return $this->redirect('/conceptos');
    }
}
