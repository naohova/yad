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
}