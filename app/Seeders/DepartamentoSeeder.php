<?php

namespace App\Seeders;

use App\Entities\Departamento;

class DepartamentoSeeder extends BaseSeeder
{
    public function data(): array
    {
        return [
            new Departamento(['nombre' => 'Gerencia General',          'descripcion' => 'Dirección estratégica de la empresa']),
            new Departamento(['nombre' => 'Tecnología de Información', 'descripcion' => 'Desarrollo y soporte de software']),
            new Departamento(['nombre' => 'Ventas',                    'descripcion' => 'Comercialización de servicios']),
            new Departamento(['nombre' => 'Contabilidad',              'descripcion' => 'Gestión financiera']),
            new Departamento(['nombre' => 'Recursos Humanos',          'descripcion' => 'Gestión del talento humano']),
            new Departamento(['nombre' => 'Operaciones',               'descripcion' => 'Ejecución de proyectos']),
        ];
    }
}
