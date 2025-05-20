<?php

namespace App\Service;

use App\Entity\Route;
use App\Entity\PlannedRoute;
use App\Entity\RoutePoint;
use App\Repository\RouteRepository;
use App\Repository\PlannedRouteRepository;
use App\Repository\MaterialRepository;
use App\Repository\RoutePointRepository;
use App\Validator\RouteValidator;
use Exception;

class RouteService
{
    public function __construct(
        private RouteRepository $routeRepository,
        private PlannedRouteRepository $plannedRouteRepository,
        private MaterialRepository $materialRepository,
        private RoutePointRepository $routePointRepository,
        private RouteValidator $validator
    ) {}

    public function createRoutePoint(array $data): RoutePoint
    {
        $this->validator->validateRoutePoint($data);

        $point = new RoutePoint();
        $point->setName($data['name']);
        $point->setDescription($data['description'] ?? null);

        $this->routePointRepository->save($point);

        return $point;
    }

    public function createPlannedRoute(array $data): PlannedRoute
    {
        $this->validator->validatePlannedRoute($data);

        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        $routePoint = $this->routePointRepository->find($data['route_point_id']);
        if (!$routePoint) {
            throw new Exception('Route point not found');
        }

        $plannedRoute = new PlannedRoute();
        $plannedRoute->setMaterialId($material->getId());
        $plannedRoute->setRoutePointId($routePoint->getId());
        $plannedRoute->setSequence($data['sequence']);
        
        if (isset($data['planned_start_date'])) {
            $plannedRoute->setExpectedAt(new \DateTime($data['planned_start_date']));
        }

        $this->plannedRouteRepository->save($plannedRoute);

        return $plannedRoute;
    }

    public function createActualRoute(array $data): Route
    {
        $this->validator->validateActualRoute($data);

        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        $routePoint = $this->routePointRepository->find($data['route_point_id']);
        if (!$routePoint) {
            throw new Exception('Route point not found');
        }

        $route = new Route();
        $route->setMaterial($material);
        $route->setRoutePoint($routePoint);
        $route->setPlannedStartDate(isset($data['planned_start_date']) ? new \DateTime($data['planned_start_date']) : null);
        $route->setPlannedEndDate(isset($data['planned_end_date']) ? new \DateTime($data['planned_end_date']) : null);
        $route->setActualStartDate(isset($data['actual_start_date']) ? new \DateTime($data['actual_start_date']) : null);
        $route->setActualEndDate(isset($data['actual_end_date']) ? new \DateTime($data['actual_end_date']) : null);

        $this->routeRepository->save($route);

        return $route;
    }

    public function getPlannedRoutes(): array
    {
        return $this->plannedRouteRepository->findAll();
    }

    public function getCompletedRoutes(): array
    {
        return $this->routeRepository->findBy(['actualEndDate' => ['not' => null]]);
    }

    public function getDelayedRoutes(): array
    {
        return $this->routeRepository->findDelayedRoutes();
    }

    public function getMaterialRoutes(int $materialId): array
    {
        return $this->routeRepository->findBy(['material' => $materialId]);
    }

    public function getAllRoutes(): array
    {
        return $this->routeRepository->findAll();
    }

    public function getRoute(int $id): Route
    {
        $route = $this->routeRepository->find($id);
        if (!$route) {
            throw new Exception('Route not found');
        }
        return $route;
    }

    public function updateRoute(int $id, array $data): Route
    {
        $route = $this->getRoute($id);
        
        if (isset($data['planned_start_date'])) {
            $route->setPlannedStartDate(new \DateTime($data['planned_start_date']));
        }
        if (isset($data['planned_end_date'])) {
            $route->setPlannedEndDate(new \DateTime($data['planned_end_date']));
        }
        if (isset($data['actual_start_date'])) {
            $route->setActualStartDate(new \DateTime($data['actual_start_date']));
        }
        if (isset($data['actual_end_date'])) {
            $route->setActualEndDate(new \DateTime($data['actual_end_date']));
        }

        $this->routeRepository->save($route);

        return $route;
    }

    public function deleteRoute(int $id): void
    {
        $route = $this->getRoute($id);
        $this->routeRepository->remove($route);
    }
} 