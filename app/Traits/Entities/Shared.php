<?php

namespace App\Traits\Entities;

use Doctrine\ORM\Mapping as ORM;
use Exception;
use InvalidArgumentException;
use ReflectionClass;

trait Shared
{
    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function toArray(): array
    {
        $rfl = new ReflectionClass($this);
        $props = $rfl->getProperties(\ReflectionProperty::IS_PUBLIC);
        $data = [];

        foreach ($props as $prop) {
            $name = $prop->getName();
            $value = $this->$name;

            if ($value instanceof \DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (is_object($value) && method_exists($value, 'toArray')) {
                $value = $value->toArray();
            }

            $data[$name] = $value;
        }

        return $data;
    }

    public function handleUpdate(array $data): void
    {
        $rfl = new ReflectionClass($this);

        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $propType = gettype($value);
                $prop = $rfl->getProperty($key);
                $expectedType = $prop->getType();
                $expectedTypeName = $expectedType?->getName();

                if ($expectedType && $expectedType->isBuiltin()) {
                    settype($value, $expectedTypeName);
                } elseif ($expectedType && class_exists($expectedTypeName)) {
                    try {
                        $value = new $expectedTypeName($value);
                        $newType = gettype($value);
                    } catch (Exception) {
                        throw new InvalidArgumentException("Un error ocurrió al crear la instancia de $expectedTypeName");
                    }
                }

                $newType = is_object($value) ? get_class($value) : gettype($value);

                if (!str_contains($newType, $expectedTypeName) && !str_contains($expectedTypeName, $newType)) {
                    $key = strtolower(str_replace('_', ' ', $key));
                    throw new InvalidArgumentException("La variable '$key' debe ser de tipo $expectedType y no de tipo $propType");
                }

                $this->$key = $value;
            }
        }
    }

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    public bool $activo = true;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    public \DateTime $fecha_creacion;

    #[ORM\PrePersist]
    public function setFechaCreacion(): void
    {
        $this->fecha_creacion = new \DateTime();
    }
}