<?php

namespace App\Seeders;

use DateTime;
use App\Entities\Cargo;
use App\Entities\Departamento;
use App\Entities\Empleado;
use App\Enums\EstadoEmpleado;
use App\Enums\Sexo;
use App\Enums\TipoContrato;

class EmpleadoSeeder extends BaseSeeder
{
    /**
     * Genera 52 empleados distribuidos en 6 departamentos siguiendo
     * exactamente la plantilla descrita en el documento del proyecto:
     * Gerencia 2, TI 18, Ventas 12, Contabilidad 5, RRHH 4, Operaciones 11.
     */
    public function data(): array
    {
        $deptos = [];
        foreach ($this->getRepo(Departamento::class)->findAll() as $d) {
            $deptos[$d->nombre] = $d;
        }

        $cargos = [];
        foreach ($this->getRepo(Cargo::class)->findAll() as $c) {
            $cargos[$c->nombre] = $c;
        }

        $plan = [
            ['Gerencia General', ['Director General' => 1, 'Asistente Ejecutiva' => 1]],
            ['Tecnología de Información', [
                'Gerente de TI' => 1, 'Desarrollador Senior' => 5,
                'Desarrollador Junior' => 6, 'Analista de Sistemas' => 3, 'Soporte Técnico' => 3,
            ]],
            ['Ventas', ['Gerente de Ventas' => 1, 'Ejecutivo de Ventas' => 11]],
            ['Contabilidad', ['Contador' => 2, 'Auxiliar Contable' => 3]],
            ['Recursos Humanos', ['Gerente de RRHH' => 1, 'Analista de RRHH' => 3]],
            ['Operaciones', ['Coordinador de Proyectos' => 3, 'Personal de Apoyo' => 8]],
        ];

        $nombresM = ['Juan', 'Carlos', 'Luis', 'Pedro', 'José', 'Miguel', 'Rafael', 'Ángel', 'Héctor', 'Manuel', 'Edward', 'Diego', 'Fernando', 'Alejandro', 'Ramón'];
        $nombresF = ['María', 'Ana', 'Laura', 'Carmen', 'Rosa', 'Elena', 'Sofía', 'Patricia', 'Daniela', 'Yesenia', 'Hensy', 'Clara', 'Lucía', 'Isabel'];
        $apellidos = ['Pérez', 'García', 'Rodríguez', 'Martínez', 'Santana', 'Jiménez', 'De los Santos', 'Reyes', 'Mejía', 'Cedano', 'Perdomo', 'Severino', 'De León', 'Mateo', 'Ramírez', 'Domínguez', 'Inoa', 'Vásquez', 'Bautista', 'Núñez'];

        $empleados = [];
        $idx = 1;
        $cedulaBase = 40212345678;

        foreach ($plan as [$deptoNombre, $cargosMap]) {
            foreach ($cargosMap as $cargoNombre => $cantidad) {
                $cargo = $cargos[$cargoNombre];

                for ($i = 0; $i < $cantidad; $i++) {
                    $sexo = $idx % 2 === 0 ? Sexo::FEMENINO : Sexo::MASCULINO;
                    $nombre = $sexo === Sexo::FEMENINO
                        ? $nombresF[array_rand($nombresF)]
                        : $nombresM[array_rand($nombresM)];
                    $apellido = $apellidos[array_rand($apellidos)] . ' ' . $apellidos[array_rand($apellidos)];

                    $empleados[] = new Empleado([
                        'cedula' => $this->formatCedula($cedulaBase + $idx),
                        'nombres' => $nombre,
                        'apellidos' => $apellido,
                        'fecha_nacimiento' => new DateTime('1985-01-01 +' . ($idx * 37) . ' days'),
                        'sexo' => $sexo,
                        'telefono' => sprintf('809-%03d-%04d', 100 + $idx, 1000 + $idx),
                        'email' => strtolower($nombre) . '.' . $idx . '@techsoft-rd.do',
                        'direccion' => 'Av. Principal #' . (100 + $idx) . ', Santo Domingo',
                        'departamento' => $deptos[$deptoNombre],
                        'cargo' => $cargo,
                        'salario' => $cargo->salario_base_sugerido,
                        'fecha_ingreso' => new DateTime('202' . rand(0, 5) . '-' . sprintf('%02d', rand(1, 12)) . '-' . sprintf('%02d', rand(1, 28))),
                        'tipo_contrato' => TipoContrato::INDEFINIDO,
                        'estado' => EstadoEmpleado::ACTIVO,
                    ]);
                    $idx++;
                }
            }
        }

        return $empleados;
    }

    private function formatCedula(int $n): string
    {
        $s = str_pad((string) $n, 11, '0', STR_PAD_LEFT);
        return substr($s, 0, 3) . '-' . substr($s, 3, 7) . '-' . substr($s, 10, 1);
    }
}
