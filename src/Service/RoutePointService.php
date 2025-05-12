<?php

namespace Service;

use Entity\RoutePoint;
use Repository\RoutePointRepository;
use Validator\RouteValidator;
use Exception;

class RoutePointService
{
    public function __construct(
        private RoutePointRepository $routePointRepository,
        private RouteValidator $validator
    ) {}

    public function createRoutePoint(array $data): RoutePoint
    {
        $this->validator->validateCreatePoint($data);
        $point = new RoutePoint();
        $point->setName($data['name']);
        $point->setType($data['type']);

        $this->routePointRepository->save($point);
        return $point;
    }

    public function updateRoutePoint(int $id, array $data): RoutePoint
    {
        $point = $this->routePointRepository->find($id);
        if (!$point) {
            throw new Exception('Route point not found');
        }

        if (isset($data['name'])) {
            $point->setName($data['name']);
        }
        if (isset($data['type'])) {
            $point->setType($data['type']);
        }

        $this->routePointRepository->save($point);
        return $point;
    }

    public function getPointsByType(string $type): array
    {
        return $this->routePointRepository->findByType($type);
    }
}