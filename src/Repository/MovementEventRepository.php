<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\MovementEvent;

class MovementEventRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, MovementEvent::class);
    }

    public function findByMaterialAndPoint(int $materialId, int $pointId): array
    {
        return $this->repository->findBy([
            'materialId' => $materialId,
            'routePointId' => $pointId
        ]);
    }

    public function findDeviations(): array
    {
        return $this->repository->findBy(['isDeviation' => true]);
    }
}