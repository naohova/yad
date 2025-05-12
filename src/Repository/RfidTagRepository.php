<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\RfidTag;

class RfidTagRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, RfidTag::class);
    }

    public function findByTagUid(string $tagUid): ?RfidTag
    {
        return $this->repository->findOneBy(['tagUid' => $tagUid]);
    }

    public function findActiveTags(): array
    {
        return $this->repository->findBy(['isActive' => true]);
    }
}