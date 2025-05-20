<?php

namespace App\Repository;

use Doctrine\ORM\EntityManager;
use App\Entity\Employee;

class EmployeeRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, Employee::class);
    }

    public function findByName(string $name): array
    {
        return $this->repository->findBy(['name' => $name]);
    }

    public function save(Employee $employee): void
    {
        $this->getEntityManager()->persist($employee);
        $this->getEntityManager()->flush();
    }

    public function remove(Employee $employee): void
    {
        $this->getEntityManager()->remove($employee);
        $this->getEntityManager()->flush();
    }
}