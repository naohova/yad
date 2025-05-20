<?php

namespace App\Validator;

use Valitron\Validator;
use Exception;

abstract class AbstractValidator
{
    protected function validate(array $data, array $rules): void
    {
        $v = new Validator($data);
        $v->rules($rules);
        
        if (!$v->validate()) {
            throw new Exception(json_encode($v->errors()));
        }
    }
} 