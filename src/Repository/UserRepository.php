<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\User;

class UserRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, User::class);
    }

    public function findByRole(string $role): array
    {
        return $this->repository->findBy(['role' => $role]);
    }

    public function findByName(string $name): ?User
    {
        return $this->repository->findOneBy(['name' => $name]);
    }
}