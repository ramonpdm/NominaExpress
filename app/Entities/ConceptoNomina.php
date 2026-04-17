<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

use App\Enums\MetodoCalculo;
use App\Enums\TipoConcepto;
use App\Traits\Entities\Shared;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'conceptos_nomina')]
class ConceptoNomina
{
    use Shared;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public int $id;

    #[ORM\Column(length: 20, unique: true)]
    public string $codigo;

    #[ORM\Column(length: 100)]
    public string $nombre;

    #[ORM\Column(type: 'string', enumType: TipoConcepto::class, length: 20)]
    public TipoConcepto $tipo = TipoConcepto::INGRESO;

    #[ORM\Column(type: 'string', enumType: MetodoCalculo::class, length: 20)]
    public MetodoCalculo $metodo_calculo = MetodoCalculo::PORCENTAJE;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 4)]
    public string $valor = '0.0000';

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    public bool $obligatorio = false;
}
