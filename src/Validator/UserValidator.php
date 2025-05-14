<?php

namespace Validator;

class UserValidator extends AbstractValidator
{
    public function validateCreate(array $data): void
    {
        $rules = [
            'required' => [
                ['name'],
                ['password'],
                ['role']
            ],
            'lengthMin' => [
                ['password', 6],
                ['name', 3]
            ],
            'lengthMax' => [
                ['name', 255],
                ['role', 50]
            ],
            'in' => [
                ['role', ['admin', 'operator', 'viewer']]
            ]
        ];

        $this->validate($data, $rules);
    }

    public function validateLogin(array $data): void
    {
        $rules = [
            'required' => [
                ['name'],
                ['password']
            ]
        ];

        $this->validate($data, $rules);
    }
} 