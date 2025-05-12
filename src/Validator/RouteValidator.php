<?php

namespace Validator;

class RouteValidator extends AbstractValidator
{
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
                ['points']
            ],
            'integer' => [
                ['material_id']
            ],
            'array' => [
                ['points']
            ]
        ];

        $this->validate($data, $rules);

        // Валидация каждой точки маршрута
        foreach ($data['points'] as $index => $point) {
            $pointRules = [
                'required' => [
                    ['route_point_id']
                ],
                'integer' => [
                    ['route_point_id']
                ],
                'datetime' => [
                    ['expected_at']
                ]
            ];

            $this->validate($point, $pointRules);
        }
    }
} 