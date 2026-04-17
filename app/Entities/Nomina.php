<?php

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Enums\EstadoNomina;
use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'nomina')]
#[ORM\UniqueConstraint(name: 'empleado_periodo_unique', columns: ['empleado_id', 'periodo_id'])]
class Nomina
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Empleado::class, inversedBy: 'nominas')]
    #[ORM\JoinColumn(name: 'empleado_id', referencedColumnName: 'id', nullable: false)]
    public Empleado $empleado;

    #[ORM\ManyToOne(targetEntity: PeriodoNomina::class, inversedBy: 'nominas')]
    #[ORM\JoinColumn(name: 'periodo_id', referencedColumnName: 'id', nullable: false)]
    public PeriodoNomina $periodo;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $salario_base = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $total_ingresos = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $total_deducciones = '0.00';

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $salario_neto = '0.00';

    #[ORM\Column(type: 'datetime')]
    public DateTime $fecha_calculo;

    #[ORM\Column(type: 'string', enumType: EstadoNomina::class, length: 20)]
    public EstadoNomina $estado = EstadoNomina::CALCULADA;

    #[ORM\OneToMany(targetEntity: NominaDetalle::class, mappedBy: 'nomina', cascade: ['persist', 'remove'])]
    public Collection $detalles;

    public function __construct(array $data = [])
    {
        $this->detalles = new ArrayCollection();
        $this->fecha_calculo = new DateTime();

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}
