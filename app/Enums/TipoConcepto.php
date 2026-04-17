<?php

namespace App\Enums;

enum TipoConcepto: string
{
    case INGRESO = 'ingreso';
    case DEDUCCION = 'deduccion';

    public function label(): string
    {
        return match ($this) {
            self::INGRESO => 'Ingreso',
            self::DEDUCCION => 'Deducción',
        };
    }

    public function signo(): int
    {
        return match ($this) {
            self::INGRESO => 1,
            self::DEDUCCION => -1,
        };
    }
}
