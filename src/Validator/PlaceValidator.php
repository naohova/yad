<?php

namespace App\Validator;

use Exception;

class PlaceValidator
{
    public function validateCreate(array $data): void
    {
        if (empty($data['name'])) {
            throw new Exception('Name is required');
        }
        if (empty($data['type'])) {
            throw new Exception('Type is required');
        }
    }

    public function validateUpdate(array $data): void
    {
        if (isset($data['name']) && empty($data['name'])) {
            throw new Exception('Name cannot be empty');
        }
        if (isset($data['type']) && empty($data['type'])) {
            throw new Exception('Type cannot be empty');
        }
    }
} 