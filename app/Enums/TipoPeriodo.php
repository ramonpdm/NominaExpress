<?php

namespace App\Enums;

enum TipoPeriodo: string
{
    case SEMANAL = 'semanal';
    case QUINCENAL = 'quincenal';
    case MENSUAL = 'mensual';

    public function label(): string
    {
        return match ($this) {
            self::SEMANAL => 'Semanal',
            self::QUINCENAL => 'Quincenal',
            self::MENSUAL => 'Mensual',
        };
    }

    public function divisorMensual(): float
    {
        return match ($this) {
            self::SEMANAL => 4.0,
            self::QUINCENAL => 2.0,
            self::MENSUAL => 1.0,
        };
    }
}
