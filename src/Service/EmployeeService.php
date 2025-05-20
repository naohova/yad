<?php

namespace App\Service;

use App\Entity\Employee;
use Doctrine\ORM\EntityManager;
use App\Validator\EmployeeValidator;
use Exception;

class EmployeeService
{
    public function __construct(
        private EntityManager $entityManager,
        private EmployeeValidator $validator
    ) {}

    public function createEmployee(array $data): Employee
    {
        $this->validator->validateCreate($data);

        $employee = new Employee();
        $employee->setName($data['name']);
        $employee->setPosition($data['position']);
        $employee->setDepartment($data['department']);
        $employee->setEmail($data['email'] ?? null);
        $employee->setPhone($data['phone'] ?? null);

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return $employee;
    }

    public function getEmployee(int $id): ?Employee
    {
        return $this->entityManager->find(Employee::class, $id);
    }

    public function getAllEmployees(): array
    {
        return $this->entityManager->getRepository(Employee::class)->findAll();
    }

    public function updateEmployee(int $id, array $data): ?Employee
    {
        $employee = $this->getEmployee($id);
        if (!$employee) {
            return null;
        }

        $this->validator->validateUpdate($data);

        if (isset($data['name'])) {
            $employee->setName($data['name']);
        }
        if (isset($data['position'])) {
            $employee->setPosition($data['position']);
        }
        if (isset($data['department'])) {
            $employee->setDepartment($data['department']);
        }
        if (isset($data['email'])) {
            $employee->setEmail($data['email']);
        }
        if (isset($data['phone'])) {
            $employee->setPhone($data['phone']);
        }

        $this->entityManager->flush();

        return $employee;
    }

    public function deleteEmployee(int $id): bool
    {
        $employee = $this->getEmployee($id);
        if (!$employee) {
            return false;
        }

        $this->entityManager->remove($employee);
        $this->entityManager->flush();

        return true;
    }
} 