<?php

namespace App\Controllers\Frontend;

use DateTime;
use Throwable;

use App\Algorithms\QuickSort;
use App\Config\Auth;
use App\Entities\Cargo;
use App\Entities\Departamento;
use App\Entities\Empleado;
use App\Enums\EstadoEmpleado;
use App\Enums\Sexo;
use App\Enums\TipoContrato;

class EmpleadosController extends BaseController
{
    public function index(): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $empleados = $em->getRepository(Empleado::class)->findBy(['activo' => true]);

        // Aplicación del algoritmo QuickSort: ordenamos por nombres ascendentemente
        // antes de pasar la colección a la vista.
        $orden = $_GET['orden'] ?? 'nombres';
        $empleados = QuickSort::sortBy($empleados, match ($orden) {
            'salario' => 'salario',
            'fecha_ingreso' => fn ($e) => $e->fecha_ingreso->getTimestamp(),
            default => 'nombres',
        }, ascendente: $orden !== 'salario');

        return $this->renderView('empleados/index', [
            'title' => 'Empleados',
            'empleados' => $empleados,
            'ordenActual' => $orden,
        ]);
    }

    public function nuevo(): string
    {
        Auth::requireEditor();

        $em = $this->orm->getProvider();

        return $this->renderView('empleados/form', [
            'title' => 'Nuevo Empleado',
            'empleado' => null,
            'departamentos' => $em->getRepository(Departamento::class)->findBy(['activo' => true]),
            'cargos' => $em->getRepository(Cargo::class)->findBy(['activo' => true]),
        ]);
    }

    public function guardar(): string
    {
        Auth::requireEditor();

        $em = $this->orm->getProvider();

        try {
            $this->validar();

            $cedula = trim($_POST['cedula'] ?? '');
            $existe = $em->getRepository(Empleado::class)->findOneBy(['cedula' => $cedula]);
            if ($existe) {
                throw new \Exception('Ya existe un empleado con la cédula ' . $cedula);
            }

            $emp = new Empleado([
                'cedula' => $cedula,
                'nombres' => $_POST['nombres'],
                'apellidos' => $_POST['apellidos'],
                'fecha_nacimiento' => new DateTime($_POST['fecha_nacimiento']),
                'sexo' => Sexo::from($_POST['sexo']),
                'telefono' => $_POST['telefono'] ?: null,
                'email' => $_POST['email'] ?: null,
                'direccion' => $_POST['direccion'] ?: null,
                'departamento' => $em->getRepository(Departamento::class)->find((int) $_POST['departamento_id']),
                'cargo' => $em->getRepository(Cargo::class)->find((int) $_POST['cargo_id']),
                'salario' => number_format((float) $_POST['salario'], 2, '.', ''),
                'fecha_ingreso' => new DateTime($_POST['fecha_ingreso']),
                'tipo_contrato' => TipoContrato::from($_POST['tipo_contrato']),
                'estado' => EstadoEmpleado::from($_POST['estado'] ?? 'activo'),
            ]);

            $em->persist($emp);
            $em->flush();

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Empleado creado exitosamente'];
            return $this->redirect('/empleados');
        } catch (Throwable $e) {
            return $this->renderView('empleados/form', [
                'title' => 'Nuevo Empleado',
                'empleado' => null,
                'error' => $e->getMessage(),
                'departamentos' => $em->getRepository(Departamento::class)->findBy(['activo' => true]),
                'cargos' => $em->getRepository(Cargo::class)->findBy(['activo' => true]),
            ]);
        }
    }

    public function ver(int $id): string
    {
        Auth::checkLogin();

        $em = $this->orm->getProvider();
        $empleado = $em->getRepository(Empleado::class)->find($id);

        if (!$empleado) return $this->renderView(404);

        return $this->renderView('empleados/ver', [
            'title' => 'Empleado: ' . $empleado->getNombreCompleto(),
            'empleado' => $empleado,
        ]);
    }

    public function editar(int $id): string
    {
        Auth::requireEditor();

        $em = $this->orm->getProvider();
        $empleado = $em->getRepository(Empleado::class)->find($id);

        if (!$empleado) return $this->renderView(404);

        return $this->renderView('empleados/form', [
            'title' => 'Editar: ' . $empleado->getNombreCompleto(),
            'empleado' => $empleado,
            'departamentos' => $em->getRepository(Departamento::class)->findBy(['activo' => true]),
            'cargos' => $em->getRepository(Cargo::class)->findBy(['activo' => true]),
        ]);
    }

    public function actualizar(int $id): string
    {
        Auth::requireEditor();

        $em = $this->orm->getProvider();
        $emp = $em->getRepository(Empleado::class)->find($id);
        if (!$emp) return $this->renderView(404);

        try {
            $this->validar();

            $emp->nombres = $_POST['nombres'];
            $emp->apellidos = $_POST['apellidos'];
            $emp->fecha_nacimiento = new DateTime($_POST['fecha_nacimiento']);
            $emp->sexo = Sexo::from($_POST['sexo']);
            $emp->telefono = $_POST['telefono'] ?: null;
            $emp->email = $_POST['email'] ?: null;
            $emp->direccion = $_POST['direccion'] ?: null;
            $emp->departamento = $em->getRepository(Departamento::class)->find((int) $_POST['departamento_id']);
            $emp->cargo = $em->getRepository(Cargo::class)->find((int) $_POST['cargo_id']);
            $emp->salario = number_format((float) $_POST['salario'], 2, '.', '');
            $emp->fecha_ingreso = new DateTime($_POST['fecha_ingreso']);
            $emp->tipo_contrato = TipoContrato::from($_POST['tipo_contrato']);
            $emp->estado = EstadoEmpleado::from($_POST['estado']);

            $em->flush();

            $_SESSION['flash'] = ['tipo' => 'success', 'msg' => 'Empleado actualizado'];
            return $this->redirect('/empleados/' . $emp->id);
        } catch (Throwable $e) {
            return $this->renderView('empleados/form', [
                'title' => 'Editar: ' . $emp->getNombreCompleto(),
                'empleado' => $emp,
                'error' => $e->getMessage(),
                'departamentos' => $em->getRepository(Departamento::class)->findBy(['activo' => true]),
                'cargos' => $em->getRepository(Cargo::class)->findBy(['activo' => true]),
            ]);
        }
    }

    private function validar(): void
    {
        $req = ['cedula', 'nombres', 'apellidos', 'fecha_nacimiento', 'sexo',
                'departamento_id', 'cargo_id', 'salario', 'fecha_ingreso', 'tipo_contrato'];

        foreach ($req as $c) {
            if (empty($_POST[$c])) {
                throw new \Exception("El campo '$c' es obligatorio");
            }
        }

        if ((float) $_POST['salario'] < 0) {
            throw new \Exception('El salario no puede ser negativo');
        }
    }
}
