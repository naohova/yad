<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\MaterialStatus;

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

    public function deleteByMaterialId(int $materialId): void
    {
        $this->entityManager->createQuery('DELETE FROM Entity\MaterialStatus ms WHERE ms.materialId = :materialId')
            ->setParameter('materialId', $materialId)
            ->execute();
    }
}