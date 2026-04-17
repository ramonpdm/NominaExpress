<?php

namespace App\Seeders;

use App\Entities\Cargo;
use App\Entities\Departamento;

class CargoSeeder extends BaseSeeder
{
    public function data(): array
    {
        $deptos = [];
        foreach ($this->getRepo(Departamento::class)->findAll() as $d) {
            $deptos[$d->nombre] = $d;
        }

        $cargos = [
            // Gerencia General
            ['nombre' => 'Director General',          'salario' => '150000.00', 'depto' => 'Gerencia General'],
            ['nombre' => 'Asistente Ejecutiva',       'salario' => '45000.00',  'depto' => 'Gerencia General'],

            // Tecnología de Información
            ['nombre' => 'Gerente de TI',             'salario' => '120000.00', 'depto' => 'Tecnología de Información'],
            ['nombre' => 'Desarrollador Senior',      'salario' => '85000.00',  'depto' => 'Tecnología de Información'],
            ['nombre' => 'Desarrollador Junior',      'salario' => '45000.00',  'depto' => 'Tecnología de Información'],
            ['nombre' => 'Analista de Sistemas',      'salario' => '55000.00',  'depto' => 'Tecnología de Información'],
            ['nombre' => 'Soporte Técnico',           'salario' => '35000.00',  'depto' => 'Tecnología de Información'],

            // Ventas
            ['nombre' => 'Gerente de Ventas',         'salario' => '95000.00',  'depto' => 'Ventas'],
            ['nombre' => 'Ejecutivo de Ventas',       'salario' => '40000.00',  'depto' => 'Ventas'],

            // Contabilidad
            ['nombre' => 'Contador',                  'salario' => '65000.00',  'depto' => 'Contabilidad'],
            ['nombre' => 'Auxiliar Contable',         'salario' => '30000.00',  'depto' => 'Contabilidad'],

            // Recursos Humanos
            ['nombre' => 'Gerente de RRHH',           'salario' => '85000.00',  'depto' => 'Recursos Humanos'],
            ['nombre' => 'Analista de RRHH',          'salario' => '40000.00',  'depto' => 'Recursos Humanos'],

            // Operaciones
            ['nombre' => 'Coordinador de Proyectos',  'salario' => '70000.00',  'depto' => 'Operaciones'],
            ['nombre' => 'Personal de Apoyo',         'salario' => '22000.00',  'depto' => 'Operaciones'],
        ];

        $entities = [];
        foreach ($cargos as $c) {
            $entities[] = new Cargo([
                'nombre' => $c['nombre'],
                'salario_base_sugerido' => $c['salario'],
                'departamento' => $deptos[$c['depto']],
            ]);
        }
        return $entities;
    }
}
