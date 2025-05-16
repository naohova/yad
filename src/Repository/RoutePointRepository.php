<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\RoutePoint;

class RoutePointRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, RoutePoint::class);
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