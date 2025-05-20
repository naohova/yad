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
        if (empty($data['name'])) {
            throw new Exception('Name is required');
        }

        $routePoint = new RoutePoint();
        $routePoint->setName($data['name']);
        
        if (isset($data['description'])) {
            $routePoint->setDescription($data['description']);
        }

        $this->routePointRepository->save($routePoint);
        return $routePoint;
    }

    public function getRoutePoint(int $id): RoutePoint
    {
        $routePoint = $this->routePointRepository->find($id);
        if (!$routePoint) {
            throw new Exception('Route point not found');
        }
        return $routePoint;
    }

    public function updateRoutePoint(int $id, array $data): RoutePoint
    {
        $routePoint = $this->routePointRepository->find($id);
        if (!$routePoint) {
            throw new Exception('Route point not found');
        }

        if (isset($data['name'])) {
            $routePoint->setName($data['name']);
        }

        if (isset($data['description'])) {
            $routePoint->setDescription($data['description']);
        }

        $routePoint->setUpdatedAt(new \DateTime());
        $this->routePointRepository->save($routePoint);
        return $routePoint;
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

    public function getAllRoutePoints(): array
    {
        return $this->routePointRepository->findActive();
    }

    public function deleteRoutePoint(int $id): void
    {
        $routePoint = $this->routePointRepository->find($id);
        if (!$routePoint) {
            throw new Exception('Route point not found');
        }

        $routePoint->setDeletedAt(new \DateTime());
        $this->routePointRepository->save($routePoint);
    }
}