<?php

namespace App\Validator;

use Exception;

class MaterialProcessValidator
{
    public function validateCreate(array $data): void
    {
        if (empty($data['material_id'])) {
            throw new Exception('Material ID is required');
        }
        if (empty($data['process_id'])) {
            throw new Exception('Process ID is required');
        }
        if (empty($data['employee_id'])) {
            throw new Exception('Employee ID is required');
        }
        if (empty($data['place_id'])) {
            throw new Exception('Place ID is required');
        }
        if (empty($data['status'])) {
            throw new Exception('Status is required');
        }
    }

    public function validateUpdate(array $data): void
    {
        if (isset($data['material_id']) && empty($data['material_id'])) {
            throw new Exception('Material ID cannot be empty');
        }
        if (isset($data['process_id']) && empty($data['process_id'])) {
            throw new Exception('Process ID cannot be empty');
        }
        if (isset($data['employee_id']) && empty($data['employee_id'])) {
            throw new Exception('Employee ID cannot be empty');
        }
        if (isset($data['place_id']) && empty($data['place_id'])) {
            throw new Exception('Place ID cannot be empty');
        }
        if (isset($data['status']) && empty($data['status'])) {
            throw new Exception('Status cannot be empty');
        }
    }
} 