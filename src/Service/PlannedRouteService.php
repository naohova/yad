<?php

namespace Service;

use Entity\PlannedRoute;
use Repository\PlannedRouteRepository;
use Repository\MaterialRepository;
use Repository\RoutePointRepository;
use Validator\RouteValidator;
use Exception;

class PlannedRouteService
{
    public function __construct(
        private PlannedRouteRepository $plannedRouteRepository,
        private MaterialRepository $materialRepository,
        private RoutePointRepository $routePointRepository,
        private RouteValidator $validator
    ) {}

    public function createRoute(array $data): array
    {
        $this->validator->validatePlanRoute($data);
        // Проверяем существование материала
        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        $routes = [];
        $this->plannedRouteRepository->beginTransaction();

        try {
            foreach ($data['points'] as $index => $point) {
                // Проверяем существование точки маршрута
                $routePoint = $this->routePointRepository->find($point['route_point_id']);
                if (!$routePoint) {
                    throw new Exception('Route point not found: ' . $point['route_point_id']);
                }

                $route = new PlannedRoute();
                $route->setMaterialId($material->getId());
                $route->setRoutePointId($routePoint->getId());
                $route->setSequence($index + 1);
                $route->setExpectedAt($point['expected_at'] ?? null);

                $this->plannedRouteRepository->save($route);
                $routes[] = $route;
            }

            $this->plannedRouteRepository->commit();
            return $routes;
        } catch (Exception $e) {
            $this->plannedRouteRepository->rollback();
            throw $e;
        }
    }

    public function getMaterialRoute(int $materialId): array
    {
        return $this->plannedRouteRepository->findByMaterial($materialId);
    }
}