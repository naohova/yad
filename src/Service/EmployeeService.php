<?php

namespace App\Service;

use App\Entity\Employee;
use Doctrine\ORM\EntityRepository;
use App\Validator\EmployeeValidator;
use Exception;

class EmployeeService
{
    public function __construct(
        private EntityRepository $employeeRepository,
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

        $this->employeeRepository->save($employee);
        return $employee;
    }

    public function getEmployee(int $id): Employee
    {
        $employee = $this->employeeRepository->find($id);
        if (!$employee) {
            throw new Exception('Employee not found');
        }
        return $employee;
    }

    public function getAllEmployees(): array
    {
        return $this->employeeRepository->findAll();
    }

    public function updateEmployee(int $id, array $data): Employee
    {
        $this->validator->validateUpdate($data);

        $employee = $this->employeeRepository->find($id);
        if (!$employee) {
            throw new Exception('Employee not found');
        }

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

        $this->employeeRepository->save($employee);
        return $employee;
    }

    public function deleteEmployee(int $id): void
    {
        $employee = $this->employeeRepository->find($id);
        if (!$employee) {
            throw new Exception('Employee not found');
        }

        $this->employeeRepository->remove($employee);
    }
} 