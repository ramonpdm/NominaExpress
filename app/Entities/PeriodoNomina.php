<?php

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Enums\EstadoPeriodo;
use App\Enums\TipoPeriodo;
use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'periodos_nomina')]
class PeriodoNomina
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 100)]
    public string $nombre;

    #[ORM\Column(type: 'string', enumType: TipoPeriodo::class, length: 20)]
    public TipoPeriodo $tipo = TipoPeriodo::QUINCENAL;

    #[ORM\Column(type: 'date')]
    public DateTime $fecha_inicio;

    #[ORM\Column(type: 'date')]
    public DateTime $fecha_fin;

    #[ORM\Column(type: 'date')]
    public DateTime $fecha_pago;

    #[ORM\Column(type: 'string', enumType: EstadoPeriodo::class, length: 20)]
    public EstadoPeriodo $estado = EstadoPeriodo::ABIERTO;

    #[ORM\OneToMany(targetEntity: Nomina::class, mappedBy: 'periodo')]
    public Collection $nominas;

    public function __construct(array $data = [])
    {
        $this->nominas = new ArrayCollection();

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
