<?php

namespace Service;

use Entity\MovementEvent;
use Entity\MaterialStatus;
use Repository\MovementEventRepository;
use Repository\MaterialStatusRepository;
use Repository\MaterialRepository;
use Repository\RoutePointRepository;
use Repository\PlannedRouteRepository;
use Validator\MovementValidator;
use Exception;

class MovementService
{
    public function __construct(
        private MovementEventRepository $movementEventRepository,
        private MaterialStatusRepository $materialStatusRepository,
        private MaterialRepository $materialRepository,
        private RoutePointRepository $routePointRepository,
        private PlannedRouteRepository $plannedRouteRepository,
        private MovementValidator $validator
    ) {}

    public function scanMaterial(array $data): MovementEvent
    {
        $this->validator->validateScan($data);
        // Проверяем существование материала
        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        // Проверяем существование точки маршрута
        $routePoint = $this->routePointRepository->find($data['route_point_id']);
        if (!$routePoint) {
            throw new Exception('Route point not found');
        }

        // Проверяем, соответствует ли точка запланированному маршруту
        $plannedRoute = $this->plannedRouteRepository->findOneBy([
            'materialId' => $material->getId(),
            'routePointId' => $routePoint->getId()
        ]);

        $isDeviation = !$plannedRoute;

        $this->movementEventRepository->beginTransaction();
        try {
            // Создаем событие перемещения
            $event = new MovementEvent();
            $event->setMaterialId($material->getId());
            $event->setRoutePointId($routePoint->getId());
            $event->setScannedBy($data['user_id']);
            $event->setScannedAt(new \DateTime());
            $event->setIsDeviation($isDeviation);
            $event->setNote($data['note'] ?? '');

            $this->movementEventRepository->save($event);

            // Получаем или создаем статус материала
            $status = $this->materialStatusRepository->findOneBy(['materialId' => $material->getId()]);
            if (!$status) {
                $status = new MaterialStatus();
                $status->setMaterial($material);
                $status->setStatus('created');
            }
            
            $status->setCurrentPointId($routePoint->getId());
            $status->setStatus($isDeviation ? 'deviation' : 'in_progress');
            $status->setUpdatedAt(new \DateTime());

            $this->materialStatusRepository->save($status);

            $this->movementEventRepository->commit();
            
            return $event;
        } catch (Exception $e) {
            $this->movementEventRepository->rollback();
            throw $e;
        }
    }

    /**
     * @return MovementEvent[]
     */
    public function getMovementHistory(int $materialId): array
    {
        $material = $this->materialRepository->find($materialId);
        if (!$material) {
            throw new Exception('Material not found');
        }
        
        return $this->movementEventRepository->findBy(
            ['materialId' => $materialId],
            ['scannedAt' => 'DESC']
        );
    }
}