<?php

namespace Validator;

class MovementValidator extends AbstractValidator
{
    public function validateScan(array $data): void
    {
        $rules = [
            'required' => [
                ['material_id'],
                ['route_point_id'],
                ['user_id']
            ],
            'integer' => [
                ['material_id'],
                ['route_point_id'],
                ['user_id']
            ],
            'lengthMax' => [
                ['note', 1000]
            ]
        ];

        $this->validate($data, $rules);
    }
} 