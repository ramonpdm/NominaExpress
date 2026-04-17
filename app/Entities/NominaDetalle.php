<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

use App\Enums\TipoConcepto;
use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'nomina_detalle')]
class NominaDetalle
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Nomina::class, inversedBy: 'detalles')]
    #[ORM\JoinColumn(name: 'nomina_id', referencedColumnName: 'id', nullable: false)]
    public Nomina $nomina;

    #[ORM\ManyToOne(targetEntity: ConceptoNomina::class)]
    #[ORM\JoinColumn(name: 'concepto_id', referencedColumnName: 'id', nullable: false)]
    public ConceptoNomina $concepto;

    #[ORM\Column(type: 'string', enumType: TipoConcepto::class, length: 20)]
    public TipoConcepto $tipo = TipoConcepto::INGRESO;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $monto = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $base_calculo = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4, nullable: true)]
    public ?string $porcentaje_aplicado = null;
}
