<?php

namespace App\Validator;

use App\Validator\AbstractValidator;

class MovementValidator extends AbstractValidator
{
    public function validateScan(array $data): void
    {
        $rules = [
            'required' => [
                ['material_id'],
                ['route_point_id'],
                ['scanned_by']
            ],
            'integer' => [
                ['material_id'],
                ['route_point_id'],
                ['scanned_by']
            ],
            'lengthMax' => [
                ['note', 1000]
            ]
        ];

        $this->validate($data, $rules);
    }
} 