<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\MaterialStatus;

class MaterialStatusRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, MaterialStatus::class);
    }

    public function findByStatus(string $status): array
    {
        return $this->repository->findBy(['status' => $status]);
    }

    public function findByCurrentPoint(int $pointId): array
    {
        return $this->repository->findBy(['currentPointId' => $pointId]);
    }
}