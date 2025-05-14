<?php

namespace Validator;

use Valitron\Validator;
use Exception;

class RouteValidator extends AbstractValidator
{
    public function __construct()
    {
        Validator::addRule('datetime', function($field, $value, array $params, array $fields) {
            if (!isset($value)) return true;
            try {
                new \DateTime($value);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }, 'must be a valid datetime string');
    }

    public function validateCreatePoint(array $data): void
    {
        $rules = [
            'required' => [
                ['name'],
                ['type']
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

    public function validatePlanRoute(array $data): void
    {
        $rules = [
            'required' => [
                ['material_id'],
                ['route_points']
            ],
            'integer' => [
                ['material_id']
            ],
            'array' => [
                ['route_points']
            ],
            'min' => [
                ['material_id', 1]
            ]
        ];

        $this->validate($data, $rules);

        if (empty($data['route_points'])) {
            throw new Exception('Route points array cannot be empty');
        }

        // Проверяем уникальность route_point_id
        $pointIds = [];
        
        // Валидация каждой точки маршрута
        foreach ($data['route_points'] as $index => $point) {
            if (!is_array($point)) {
                throw new Exception('Each route point must be an array');
            }

            $pointRules = [
                'required' => [
                    ['route_point_id']
                ],
                'integer' => [
                    ['route_point_id']
                ],
                'min' => [
                    ['route_point_id', 1]
                ]
            ];

            if (isset($point['expected_at'])) {
                if (!is_string($point['expected_at'])) {
                    throw new Exception('expected_at must be a string');
                }
                $pointRules['datetime'] = [['expected_at']];
            }

            $this->validate($point, $pointRules);

            // Проверяем уникальность route_point_id
            if (in_array($point['route_point_id'], $pointIds)) {
                throw new Exception('Duplicate route_point_id found: ' . $point['route_point_id']);
            }
            $pointIds[] = $point['route_point_id'];
        }
    }
} 