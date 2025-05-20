<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\RoutePoint;

class RoutePointRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, RoutePoint::class);
    }

    public function findByName(string $name): array
    {
        return $this->repository->findBy(['name' => $name]);
    }

    public function findActive(): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        return $qb->select('rp')
            ->from('App\Entity\RoutePoint', 'rp')
            ->where('rp.deletedAt IS NULL')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->repository->findBy(['type' => $type]);
    }

    public function findByRoutePoint(int $id): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('pr')
           ->from('Entity\PlannedRoute', 'pr')
           ->where('pr.routePointId = :id')
           ->setParameter('id', $id);
        
        return $qb->getQuery()->getResult();
    }
}