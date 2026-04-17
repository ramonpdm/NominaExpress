<?php

namespace App\Enums;

enum EstadoNomina: string
{
    case CALCULADA = 'calculada';
    case PAGADA = 'pagada';
    case ANULADA = 'anulada';

    public function label(): string
    {
        return match ($this) {
            self::CALCULADA => 'Calculada',
            self::PAGADA => 'Pagada',
            self::ANULADA => 'Anulada',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::CALCULADA => 'primary',
            self::PAGADA => 'success',
            self::ANULADA => 'danger',
        };
    }
}
