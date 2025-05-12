<?php

namespace Validator;

class DocumentValidator extends AbstractValidator
{
    public function validateUpload(array $data): void
    {
        $rules = [
            'required' => [
                ['material_id'],
                ['type']
            ],
            'integer' => [
                ['material_id']
            ],
            'lengthMax' => [
                ['type', 50]
            ]
        ];

        $this->validate($data, $rules);
    }
} 