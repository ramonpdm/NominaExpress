<?php

namespace App\Enums;

enum RolUsuario: string
{
    case ADMIN = 'admin';
    case RRHH = 'rrhh';
    case CONSULTA = 'consulta';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrador',
            self::RRHH => 'Recursos Humanos',
            self::CONSULTA => 'Consulta',
        };
    }

    public function puedeEditar(): bool
    {
        return $this === self::ADMIN || $this === self::RRHH;
    }

    public function puedeAdministrar(): bool
    {
        return $this === self::ADMIN;
    }
}
