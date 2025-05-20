<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\PlannedRoute;

class PlannedRouteRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, PlannedRoute::class);
    }

    public function findByMaterial(int $materialId): array
    {
        return $this->repository->findBy(
            ['materialId' => $materialId],
            ['sequence' => 'ASC']
        );
    }

    public function findByRoutePoint(int $routePointId): array
    {
        return $this->repository->findBy(['routePointId' => $routePointId]);
    }
}