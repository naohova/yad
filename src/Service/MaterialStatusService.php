<?php

namespace App\Service;

use App\Entity\MaterialStatus;
use App\Repository\MaterialStatusRepository;
use App\Repository\MaterialRepository;
use App\Repository\RoutePointRepository;
use Exception;

class MaterialStatusService
{
    public function __construct(
        private MaterialStatusRepository $materialStatusRepository,
        private MaterialRepository $materialRepository,
        private RoutePointRepository $routePointRepository
    ) {}

    public function updateStatus(int $materialId, array $data): MaterialStatus
    {
        // Проверяем существование материала
        $material = $this->materialRepository->find($materialId);
        if (!$material) {
            throw new Exception('Material not found');
        }

        // Проверяем существование точки маршрута
        if (isset($data['current_point_id'])) {
            $point = $this->routePointRepository->find($data['current_point_id']);
            if (!$point) {
                throw new Exception('Route point not found');
            }
        }

        $status = $this->materialStatusRepository->findOneBy(['materialId' => $materialId]);
        if (!$status) {
            $status = new MaterialStatus();
            $status->setMaterialId($materialId);
        }

        if (isset($data['status'])) {
            $status->setStatus($data['status']);
        }
        if (isset($data['current_point_id'])) {
            $status->setCurrentPointId($data['current_point_id']);
        }
        $status->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));

        $this->materialStatusRepository->save($status);
        return $status;
    }

    public function getMaterialsInStatus(string $status): array
    {
        $statuses = $this->materialStatusRepository->findByStatus($status);
        return array_map(function($status) {
            return [
                'material_id' => $status->getMaterialId(),
                'status' => $status->getStatus(),
                'current_point_id' => $status->getCurrentPointId(),
                'updated_at' => $status->getUpdatedAt()->format('Y-m-d H:i:s')
            ];
        }, $statuses);
    }
}