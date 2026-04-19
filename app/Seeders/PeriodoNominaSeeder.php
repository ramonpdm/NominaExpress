<?php

namespace App\Seeders;

use DateTime;
use App\Entities\PeriodoNomina;
use App\Enums\EstadoPeriodo;
use App\Enums\TipoPeriodo;

class PeriodoNominaSeeder extends BaseSeeder
{
    public function data(): array
    {
        return [
            new PeriodoNomina([
                'nombre' => '1ra Quincena Marzo 2026',
                'tipo' => TipoPeriodo::QUINCENAL,
                'fecha_inicio' => new DateTime('2026-03-01'),
                'fecha_fin' => new DateTime('2026-03-15'),
                'fecha_pago' => new DateTime('2026-03-16'),
                'estado' => EstadoPeriodo::PAGADO,
            ]),
            new PeriodoNomina([
                'nombre' => '2da Quincena Marzo 2026',
                'tipo' => TipoPeriodo::QUINCENAL,
                'fecha_inicio' => new DateTime('2026-03-16'),
                'fecha_fin' => new DateTime('2026-03-31'),
                'fecha_pago' => new DateTime('2026-04-01'),
                'estado' => EstadoPeriodo::PAGADO,
            ]),
            new PeriodoNomina([
                'nombre' => '1ra Quincena Abril 2026',
                'tipo' => TipoPeriodo::QUINCENAL,
                'fecha_inicio' => new DateTime('2026-04-01'),
                'fecha_fin' => new DateTime('2026-04-15'),
                'fecha_pago' => new DateTime('2026-04-16'),
                'estado' => EstadoPeriodo::ABIERTO,
            ]),
        ];
    }

    public function run(): void
    {
        $periodos = $this->data();
        
        // Primero persistimos los períodos
        foreach ($periodos as $periodo) {
            $this->entityManager->persist($periodo);
        }
        $this->entityManager->flush();

        // Luego procesamos las nóminas para los períodos pagados
        $calc = new \App\Services\NominaCalculator($this->entityManager);
        foreach ($periodos as $periodo) {
            if ($periodo->estado === EstadoPeriodo::PAGADO) {
                $calc->procesarPeriodo($periodo);
            }
        }
    }
}
