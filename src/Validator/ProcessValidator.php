<?php

namespace App\Validator;

use Exception;

class ProcessValidator
{
    public function validateCreate(array $data): void
    {
        if (empty($data['name'])) {
            throw new Exception('Name is required');
        }
        if (isset($data['duration_minutes']) && !is_numeric($data['duration_minutes'])) {
            throw new Exception('Duration must be a number');
        }
    }

    public function validateUpdate(array $data): void
    {
        if (isset($data['name']) && empty($data['name'])) {
            throw new Exception('Name cannot be empty');
        }
        if (isset($data['duration_minutes']) && !is_numeric($data['duration_minutes'])) {
            throw new Exception('Duration must be a number');
        }
    }
} 