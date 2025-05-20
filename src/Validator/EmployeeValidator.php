<?php

namespace App\Validator;

use Exception;

class EmployeeValidator
{
    public function validateCreate(array $data): void
    {
        if (empty($data['name'])) {
            throw new Exception('Name is required');
        }
        if (empty($data['position'])) {
            throw new Exception('Position is required');
        }
        if (empty($data['department'])) {
            throw new Exception('Department is required');
        }
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
    }

    public function validateUpdate(array $data): void
    {
        if (isset($data['name']) && empty($data['name'])) {
            throw new Exception('Name cannot be empty');
        }
        if (isset($data['position']) && empty($data['position'])) {
            throw new Exception('Position cannot be empty');
        }
        if (isset($data['department']) && empty($data['department'])) {
            throw new Exception('Department cannot be empty');
        }
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
    }
} 