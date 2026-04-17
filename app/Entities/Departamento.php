<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'departamentos')]
class Departamento
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 100, unique: true)]
    public string $nombre;

    #[ORM\Column(length: 255, nullable: true)]
    public ?string $descripcion = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subdepartamentos')]
    #[ORM\JoinColumn(name: 'padre_id', referencedColumnName: 'id', nullable: true)]
    public ?Departamento $padre = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'padre')]
    public Collection $subdepartamentos;

    #[ORM\OneToMany(targetEntity: Cargo::class, mappedBy: 'departamento')]
    public Collection $cargos;

    #[ORM\OneToMany(targetEntity: Empleado::class, mappedBy: 'departamento')]
    public Collection $empleados;

    public function __construct(array $data = [])
    {
        $this->subdepartamentos = new ArrayCollection();
        $this->cargos = new ArrayCollection();
        $this->empleados = new ArrayCollection();

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
