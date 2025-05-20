<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository
{
    protected EntityRepository $repository;
    protected EntityManager $entityManager;
    protected string $entityClass;

    public function __construct(EntityManager $entityManager, string $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
        $this->repository = $entityManager->getRepository($entityClass);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findBy(array $criteria, ?array $orderBy = null): array
    {
        return $this->repository->findBy($criteria, $orderBy);
    }

    public function findOneBy(array $criteria): ?object
    {
        return $this->repository->findOneBy($criteria);
    }

    public function save($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function remove($entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function beginTransaction(): void
    {
        $this->entityManager->beginTransaction();
    }

    public function commit(): void
    {
        $this->entityManager->commit();
    }

    public function rollback(): void
    {
        $this->entityManager->rollback();
    }
}