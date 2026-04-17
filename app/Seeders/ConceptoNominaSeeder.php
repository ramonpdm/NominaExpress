<?php

namespace App\Seeders;

use App\Entities\ConceptoNomina;
use App\Enums\MetodoCalculo;
use App\Enums\TipoConcepto;

class ConceptoNominaSeeder extends BaseSeeder
{
    public function data(): array
    {
        return [
            new ConceptoNomina([
                'codigo' => 'SAL_BASE',
                'nombre' => 'Salario Base',
                'tipo' => TipoConcepto::INGRESO,
                'metodo_calculo' => MetodoCalculo::FORMULA,
                'valor' => '0.0000',
                'obligatorio' => false,
            ]),
            new ConceptoNomina([
                'codigo' => 'AFP',
                'nombre' => 'Administradora de Fondos de Pensiones',
                'tipo' => TipoConcepto::DEDUCCION,
                'metodo_calculo' => MetodoCalculo::PORCENTAJE,
                'valor' => '2.8700',
                'obligatorio' => true,
            ]),
            new ConceptoNomina([
                'codigo' => 'ARS',
                'nombre' => 'Administradora de Riesgos de Salud',
                'tipo' => TipoConcepto::DEDUCCION,
                'metodo_calculo' => MetodoCalculo::PORCENTAJE,
                'valor' => '3.0400',
                'obligatorio' => true,
            ]),
            new ConceptoNomina([
                'codigo' => 'ISR',
                'nombre' => 'Impuesto Sobre la Renta',
                'tipo' => TipoConcepto::DEDUCCION,
                'metodo_calculo' => MetodoCalculo::FORMULA,
                'valor' => '0.0000',
                'obligatorio' => true,
            ]),
            new ConceptoNomina([
                'codigo' => 'BONO',
                'nombre' => 'Bonificación',
                'tipo' => TipoConcepto::INGRESO,
                'metodo_calculo' => MetodoCalculo::MONTO_FIJO,
                'valor' => '0.0000',
                'obligatorio' => false,
            ]),
            new ConceptoNomina([
                'codigo' => 'HORA_EXT',
                'nombre' => 'Horas Extras',
                'tipo' => TipoConcepto::INGRESO,
                'metodo_calculo' => MetodoCalculo::MONTO_FIJO,
                'valor' => '0.0000',
                'obligatorio' => false,
            ]),
        ];
    }
}
