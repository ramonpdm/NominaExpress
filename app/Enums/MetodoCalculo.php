<?php

namespace App\Enums;

enum MetodoCalculo: string
{
    case PORCENTAJE = 'porcentaje';
    case MONTO_FIJO = 'monto_fijo';
    case FORMULA = 'formula';

    public function label(): string
    {
        return match ($this) {
            self::PORCENTAJE => 'Porcentaje',
            self::MONTO_FIJO => 'Monto Fijo',
            self::FORMULA => 'Fórmula',
        };
    }
}
