<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Entity\Document;

class DocumentRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, Document::class);
    }

    public function findByMaterialId(int $materialId): array
    {
        return $this->repository->findBy(['materialId' => $materialId]);
    }

    public function findByType(string $type): array
    {
        return $this->repository->findBy(['type' => $type]);
    }
}