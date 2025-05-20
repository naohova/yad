<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\Process;

class ProcessRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, Process::class);
    }

    public function findByType(string $type): array
    {
        return $this->repository->findBy(['type' => $type]);
    }

    public function save(Process $process): void
    {
        $this->getEntityManager()->persist($process);
        $this->getEntityManager()->flush();
    }

    public function remove(Process $process): void
    {
        $this->getEntityManager()->remove($process);
        $this->getEntityManager()->flush();
    }
} 