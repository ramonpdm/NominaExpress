<?php

namespace App\Entities;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Enums\EstadoEmpleado;
use App\Enums\Sexo;
use App\Enums\TipoContrato;
use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'empleados')]
class Empleado
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 20, unique: true)]
    public string $cedula;

    #[ORM\Column(length: 100)]
    public string $nombres;

    #[ORM\Column(length: 100)]
    public string $apellidos;

    #[ORM\Column(type: 'date')]
    public DateTime $fecha_nacimiento;

    #[ORM\Column(type: 'string', enumType: Sexo::class, length: 1)]
    public Sexo $sexo = Sexo::MASCULINO;

    #[ORM\Column(length: 20, nullable: true)]
    public ?string $telefono = null;

    #[ORM\Column(length: 150, nullable: true)]
    public ?string $email = null;

    #[ORM\Column(type: 'text', nullable: true)]
    public ?string $direccion = null;

    #[ORM\ManyToOne(targetEntity: Departamento::class, inversedBy: 'empleados')]
    #[ORM\JoinColumn(name: 'departamento_id', referencedColumnName: 'id', nullable: false)]
    public Departamento $departamento;

    #[ORM\ManyToOne(targetEntity: Cargo::class, inversedBy: 'empleados')]
    #[ORM\JoinColumn(name: 'cargo_id', referencedColumnName: 'id', nullable: false)]
    public Cargo $cargo;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    public string $salario = '0.00';

    #[ORM\Column(type: 'date')]
    public DateTime $fecha_ingreso;

    #[ORM\Column(type: 'string', enumType: TipoContrato::class, length: 20)]
    public TipoContrato $tipo_contrato = TipoContrato::INDEFINIDO;

    #[ORM\Column(type: 'string', enumType: EstadoEmpleado::class, length: 20)]
    public EstadoEmpleado $estado = EstadoEmpleado::ACTIVO;

    #[ORM\OneToMany(targetEntity: Nomina::class, mappedBy: 'empleado')]
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

    public function getNombreCompleto(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function getSalarioFloat(): float
    {
        return (float) $this->salario;
    }
}
