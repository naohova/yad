<?php

namespace App\Service;

use App\Entity\Material;
use App\Entity\MaterialProcess;
use App\Entity\MaterialStatus;
use App\Entity\RoutePoint;
use App\Repository\MaterialRepository;
use App\Repository\MaterialProcessRepository;
use App\Repository\MaterialStatusRepository;
use App\Repository\RoutePointRepository;
use Exception;

class MaterialInfoService
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private MaterialProcessRepository $materialProcessRepository,
        private MaterialStatusRepository $materialStatusRepository,
        private RoutePointRepository $routePointRepository
    ) {}

    public function getMaterialsWithLocationAndProcess(): array
    {
        $materials = $this->materialRepository->findAll();
        $result = [];

        foreach ($materials as $material) {
            $materialInfo = [
                'id' => $material->getId(),
                'name' => $material->getName(),
                'amount' => $material->getAmount(),
                'type' => $material->getType(),
                'part_number' => $material->getPartNumber(),
                'created_at' => $material->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
                'updated_at' => $material->getUpdatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            ];

            // Добавляем информацию о текущем местоположении
            $status = $material->getStatus();
            if ($status) {
                $currentPoint = $this->routePointRepository->find($status->getCurrentPointId());
                $materialInfo['current_location'] = $currentPoint ? [
                    'point_id' => $currentPoint->getId(),
                    'name' => $currentPoint->getName(),
                    'type' => $currentPoint->getType(),
                    'description' => $currentPoint->getDescription()
                ] : null;
                $materialInfo['status'] = $status->getStatus();
            }

            // Получаем текущий/последний процесс для материала
            $currentProcess = $this->materialProcessRepository->findOneBy(
                ['material' => $material->getId()],
                ['plannedStart' => 'DESC']
            );

            if ($currentProcess) {
                $process = $currentProcess->getProcess();
                $place = $currentProcess->getPlace();
                $employee = $currentProcess->getEmployee();

                $materialInfo['current_process'] = [
                    'id' => $process->getId(),
                    'name' => $process->getName(),
                    'status' => $currentProcess->getStatus(),
                    'planned_start' => $currentProcess->getPlannedStart()?->format('Y-m-d\TH:i:s.u\Z'),
                    'planned_end' => $currentProcess->getPlannedEnd()?->format('Y-m-d\TH:i:s.u\Z'),
                    'fact_start' => $currentProcess->getFactStart()?->format('Y-m-d\TH:i:s.u\Z'),
                    'fact_end' => $currentProcess->getFactEnd()?->format('Y-m-d\TH:i:s.u\Z'),
                    'place' => [
                        'id' => $place->getId(),
                        'name' => $place->getName(),
                        'type' => $place->getType()
                    ],
                    'employee' => [
                        'id' => $employee->getId(),
                        'name' => $employee->getName()
                    ]
                ];
            }

            $result[] = $materialInfo;
        }

        return $result;
    }

    public function getMaterialInfo(int $materialId): array
    {
        $material = $this->materialRepository->find($materialId);
        if (!$material) {
            throw new Exception('Material not found');
        }

        // Получаем базовую информацию о материале
        $materialInfo = [
            'id' => $material->getId(),
            'name' => $material->getName(),
            'amount' => $material->getAmount(),
            'type' => $material->getType(),
            'part_number' => $material->getPartNumber(),
            'created_at' => $material->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $material->getUpdatedAt()->format('Y-m-d\TH:i:s.u\Z')
        ];

        // Добавляем информацию о текущем местоположении
        $status = $material->getStatus();
        if ($status) {
            $currentPoint = $this->routePointRepository->find($status->getCurrentPointId());
            $materialInfo['current_location'] = $currentPoint ? [
                'point_id' => $currentPoint->getId(),
                'name' => $currentPoint->getName(),
                'type' => $currentPoint->getType(),
                'description' => $currentPoint->getDescription()
            ] : null;
            $materialInfo['status'] = $status->getStatus();
        }

        // Получаем историю процессов для материала
        $processes = $this->materialProcessRepository->findByMaterial($materialId);
        $materialInfo['processes'] = array_map(function(MaterialProcess $materialProcess) {
            $process = $materialProcess->getProcess();
            $place = $materialProcess->getPlace();
            $employee = $materialProcess->getEmployee();

            return [
                'id' => $process->getId(),
                'name' => $process->getName(),
                'status' => $materialProcess->getStatus(),
                'planned_start' => $materialProcess->getPlannedStart()?->format('Y-m-d\TH:i:s.u\Z'),
                'planned_end' => $materialProcess->getPlannedEnd()?->format('Y-m-d\TH:i:s.u\Z'),
                'fact_start' => $materialProcess->getFactStart()?->format('Y-m-d\TH:i:s.u\Z'),
                'fact_end' => $materialProcess->getFactEnd()?->format('Y-m-d\TH:i:s.u\Z'),
                'place' => [
                    'id' => $place->getId(),
                    'name' => $place->getName(),
                    'type' => $place->getType()
                ],
                'employee' => [
                    'id' => $employee->getId(),
                    'name' => $employee->getName()
                ]
            ];
        }, $processes);

        return $materialInfo;
    }
} 