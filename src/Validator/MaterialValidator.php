<?php

namespace Validator;

class MaterialValidator extends AbstractValidator
{
    public function validateCreate(array $data): void
    {
        $rules = [
            'required' => [
                ['name'],
                ['amount'],
                ['type'],
                ['rfid_tag'],
                ['initial_point_id']
            ],
            'integer' => [
                ['amount'],
                ['initial_point_id']
            ],
            'min' => [
                ['amount', 0]
            ],
            'lengthMin' => [
                ['name', 1]
            ],
            'lengthMax' => [
                ['name', 255],
                ['type', 50]
            ]
        ];

        $this->validate($data, $rules);
    }

    public function validateUpdate(array $data): void
    {
        $rules = [
            'integer' => [
                ['amount']
            ],
            'min' => [
                ['amount', 0]
            ],
            'lengthMax' => [
                ['name', 255],
                ['type', 50]
            ]
        ];

        $this->validate($data, $rules);
    }
} 