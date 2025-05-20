<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\MaterialProcess;

class MaterialProcessRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, MaterialProcess::class);
    }

    public function findByMaterial(int $materialId): array
    {
        return $this->repository->findBy(['material' => $materialId]);
    }

    public function findByEmployee(int $employeeId): array
    {
        return $this->repository->findBy(['employee' => $employeeId]);
    }

    public function findByPlace(int $placeId): array
    {
        return $this->repository->findBy(['place' => $placeId]);
    }
} 