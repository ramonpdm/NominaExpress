<?php

namespace App\Entities;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

use App\Enums\RolUsuario;
use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'usuarios')]
class Usuario
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 50, unique: true)]
    public string $username;

    #[ORM\Column(length: 255)]
    public string $password;

    #[ORM\Column(length: 100)]
    public string $nombre;

    #[ORM\Column(length: 100)]
    public string $apellido;

    #[ORM\Column(length: 150, unique: true)]
    public string $email;

    #[ORM\Column(type: 'string', enumType: RolUsuario::class, length: 20)]
    public RolUsuario $rol = RolUsuario::CONSULTA;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?DateTime $ultimo_acceso = null;

    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function isAdmin(): bool
    {
        return $this->rol === RolUsuario::ADMIN;
    }

    public function isRRHH(): bool
    {
        return $this->rol === RolUsuario::RRHH;
    }

    public function puedeEditar(): bool
    {
        return $this->rol->puedeEditar();
    }
}
