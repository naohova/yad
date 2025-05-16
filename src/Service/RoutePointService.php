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

    public function getRoutePoint(int $id): array
    {
        $point = $this->routePointRepository->find($id);
        if (!$point) {
            throw new Exception('Route point not found');
        }
        
        return [
            'id' => $point->getId(),
            'name' => $point->getName(),
            'type' => $point->getType()
        ];
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
        $points = $this->routePointRepository->findByType($type);
        return array_map(function($point) {
            return [
                'id' => $point->getId(),
                'name' => $point->getName(),
                'type' => $point->getType()
            ];
        }, $points);
    }

    public function getAllPoints(): array
    {
        $points = $this->routePointRepository->findAll();
        return array_map(function($point) {
            return [
                'id' => $point->getId(),
                'name' => $point->getName(),
                'type' => $point->getType()
            ];
        }, $points);
    }

    public function deletePoint(int $id): void
    {
        $point = $this->routePointRepository->find($id);
        if (!$point) {
            throw new Exception('Route point not found');
        }
        
        // Проверяем, не используется ли точка в маршрутах
        $routes = $this->routePointRepository->findByRoutePoint($id);
        if (!empty($routes)) {
            throw new Exception('Cannot delete route point that is used in routes');
        }
        
        $this->routePointRepository->delete($point);
    }
}