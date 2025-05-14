<?php

namespace Service;

use Entity\PlannedRoute;
use Entity\MaterialStatus;
use Repository\PlannedRouteRepository;
use Repository\MaterialRepository;
use Repository\RoutePointRepository;
use Repository\MaterialStatusRepository;
use Validator\RouteValidator;
use Exception;

class PlannedRouteService
{
    public function __construct(
        private PlannedRouteRepository $plannedRouteRepository,
        private MaterialRepository $materialRepository,
        private RoutePointRepository $routePointRepository,
        private MaterialStatusRepository $materialStatusRepository,
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
            // Проверяем существование статуса материала
            $status = $material->getStatus();
            if (!$status) {
                throw new Exception('Material status not found. Please initialize material status first.');
            }

            foreach ($data['route_points'] as $index => $point) {
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
                $routes[] = [
                    'id' => $route->getId(),
                    'material_id' => $route->getMaterialId(),
                    'route_point_id' => $route->getRoutePointId(),
                    'sequence' => $route->getSequence(),
                    'expected_at' => $route->getExpectedAt() ? $route->getExpectedAt()->format('Y-m-d\TH:i:s.u\Z') : null
                ];
            }

            // Обновляем статус материала
            $firstPoint = $data['route_points'][0];
            $status->setCurrentPointId($firstPoint['route_point_id']);
            $status->setStatus('in_progress');
            $status->setUpdatedAt(new \DateTime());

            $this->materialStatusRepository->save($status);

            $this->plannedRouteRepository->commit();
            return $routes;
        } catch (Exception $e) {
            $this->plannedRouteRepository->rollback();
            throw $e;
        }
    }

    public function getMaterialRoute(int $materialId): array
    {
        $routes = $this->plannedRouteRepository->findBy(['materialId' => $materialId], ['sequence' => 'ASC']);
        
        return array_map(function($route) {
            return [
                'id' => $route->getId(),
                'material_id' => $route->getMaterialId(),
                'route_point_id' => $route->getRoutePointId(),
                'sequence' => $route->getSequence(),
                'expected_at' => $route->getExpectedAt()->format('Y-m-d\TH:i:s.u\Z')
            ];
        }, $routes);
    }
}