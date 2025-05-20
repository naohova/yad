<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\Route;

class RouteRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, Route::class);
    }

    public function findByMaterialId(int $materialId): array
    {
        return $this->repository->findBy(['material' => $materialId]);
    }

    public function findByRoutePointId(int $routePointId): array
    {
        return $this->repository->findBy(['routePoint' => $routePointId]);
    }

    public function findActive(): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('r')
            ->from('App\Entity\Route', 'r')
            ->where('r.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findPlannedRoutes(): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('r')
            ->from('App\Entity\Route', 'r')
            ->where('r.plannedStartDate IS NOT NULL')
            ->andWhere('r.actualEndDate IS NULL')
            ->andWhere('r.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findCompletedRoutes(): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('r')
            ->from('App\Entity\Route', 'r')
            ->where('r.actualEndDate IS NOT NULL')
            ->andWhere('r.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findDelayedRoutes(): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('r')
            ->from('App\Entity\Route', 'r')
            ->where('r.actualEndDate > r.plannedEndDate')
            ->andWhere('r.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }
} 