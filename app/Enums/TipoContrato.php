<?php

namespace App\Enums;

enum TipoContrato: string
{
    case INDEFINIDO = 'indefinido';
    case TEMPORAL = 'temporal';
    case PASANTIA = 'pasantia';

    public function label(): string
    {
        return match ($this) {
            self::INDEFINIDO => 'Indefinido',
            self::TEMPORAL => 'Temporal',
            self::PASANTIA => 'Pasantía',
        };
    }
}
