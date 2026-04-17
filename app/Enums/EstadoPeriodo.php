<?php

namespace App\Enums;

enum EstadoPeriodo: string
{
    case ABIERTO = 'abierto';
    case CERRADO = 'cerrado';
    case PAGADO = 'pagado';

    public function label(): string
    {
        return match ($this) {
            self::ABIERTO => 'Abierto',
            self::CERRADO => 'Cerrado',
            self::PAGADO => 'Pagado',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ABIERTO => 'primary',
            self::CERRADO => 'secondary',
            self::PAGADO => 'success',
        };
    }
}
