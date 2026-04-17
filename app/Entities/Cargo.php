<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'cargos')]
class Cargo
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 100)]
    public string $nombre;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $salario_base_sugerido = '0.00';

    #[ORM\ManyToOne(targetEntity: Departamento::class, inversedBy: 'cargos')]
    #[ORM\JoinColumn(name: 'departamento_id', referencedColumnName: 'id', nullable: false)]
    public Departamento $departamento;

    #[ORM\OneToMany(targetEntity: Empleado::class, mappedBy: 'cargo')]
    public Collection $empleados;

    public function __construct(array $data = [])
    {
        $this->empleados = new ArrayCollection();

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
