<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\Material;

class MaterialRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, Material::class);
    }

    public function findByType(string $type): array
    {
        return $this->repository->findBy(['type' => $type]);
    }

    public function findByName(string $name): array
    {
        return $this->repository->findBy(['name' => $name]);
    }
}