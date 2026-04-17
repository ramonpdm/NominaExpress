<?php

namespace App\Seeders;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class BaseSeeder
{
    const array ORDER = [
        1 => DepartamentoSeeder::class,
        2 => CargoSeeder::class,
        3 => ConceptoNominaSeeder::class,
        4 => UsuarioSeeder::class,
        5 => EmpleadoSeeder::class,
        6 => PeriodoNominaSeeder::class,
    ];

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function data(): array
    {
        return [];
    }

    public function run(): void
    {
        foreach ($this->data() as $entity) {
            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();
    }

    public function getRepo(string $entityClass): EntityRepository
    {
        return $this->entityManager->getRepository($entityClass);
    }
}
