<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\Place;

class PlaceRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, Place::class);
    }

    public function findByType(string $type): array
    {
        return $this->repository->findBy(['placeType' => $type]);
    }
} 