<?php

namespace App\Enums;

enum EstadoEmpleado: string
{
    case ACTIVO = 'activo';
    case INACTIVO = 'inactivo';
    case SUSPENDIDO = 'suspendido';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVO => 'Activo',
            self::INACTIVO => 'Inactivo',
            self::SUSPENDIDO => 'Suspendido',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ACTIVO => 'success',
            self::INACTIVO => 'secondary',
            self::SUSPENDIDO => 'warning',
        };
    }
}
