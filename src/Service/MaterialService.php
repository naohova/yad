<?php

namespace Service;

use Entity\Material;
use Entity\MaterialStatus;
use Repository\MaterialRepository;
use Repository\RfidTagRepository;
use Repository\MaterialStatusRepository;
use Exception;
use \Validator\MaterialValidator;

class MaterialService
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private RfidTagRepository $rfidTagRepository,
        private MaterialStatusRepository $materialStatusRepository,
        private MaterialValidator $validator
    ) {}

    public function createMaterial(array $data): array
    {
        $this->validator->validateCreate($data);

        $material = new Material();
        $material->setName($data['name']);
        $material->setAmount($data['amount']);
        $material->setType($data['type']);

        $this->materialRepository->beginTransaction();
        try {
            $this->materialRepository->save($material);
            
            // Создаем начальный статус для материала
            $status = new MaterialStatus();
            $status->setMaterialId($material->getId());
            $status->setStatus('created');
            $status->setCurrentPointId($data['initial_point_id']);
            $status->setUpdatedAt(new \DateTime());
            
            $this->materialStatusRepository->save($status);
            
            $this->materialRepository->commit();
            
            return [
                'id' => $material->getId(),
                'name' => $material->getName(),
                'amount' => $material->getAmount(),
                'type' => $material->getType()
            ];
        } catch (Exception $e) {
            $this->materialRepository->rollback();
            throw $e;
        }
    }

    public function updateMaterial(int $id, array $data): array
    {
        $material = $this->materialRepository->find($id);
        if (!$material) {
            throw new Exception('Material not found');
        }

        if (isset($data['name'])) {
            $material->setName($data['name']);
        }
        if (isset($data['amount'])) {
            $material->setAmount($data['amount']);
        }
        if (isset($data['type'])) {
            $material->setType($data['type']);
        }

        $this->materialRepository->save($material);
        
        return [
            'id' => $material->getId(),
            'name' => $material->getName(),
            'amount' => $material->getAmount(),
            'type' => $material->getType()
        ];
    }

    public function getMaterialWithStatus(int $id): array
    {
        $material = $this->materialRepository->find($id);
        if (!$material) {
            throw new Exception('Material not found', 404);
        }

        $status = $this->materialStatusRepository->findOneBy(['materialId' => $id]);
        if (!$status) {
            throw new Exception('Material status not found', 404);
        }
        
        return [
            'id' => $material->getId(),
            'name' => $material->getName(),
            'amount' => $material->getAmount(),
            'type' => $material->getType(),
            'status' => [
                'status' => $status->getStatus(),
                'current_point_id' => $status->getCurrentPointId(),
                'updated_at' => $status->getUpdatedAt()->format('Y-m-d H:i:s')
            ]
        ];
    }

    public function deleteMaterial(int $id): void
    {
        $material = $this->materialRepository->find($id);
        if (!$material) {
            throw new Exception('Material not found');
        }

        $this->materialRepository->beginTransaction();
        try {
            // Удаляем все связанные статусы
            $this->materialStatusRepository->deleteByMaterialId($id);
            
            // Удаляем материал
            $this->materialRepository->delete($material);
            
            $this->materialRepository->commit();
        } catch (Exception $e) {
            $this->materialRepository->rollback();
            throw $e;
        }
    }

    public function getAllMaterials(): array
    {
        $materials = $this->materialRepository->findAll();
        return array_map(function($material) {
            return [
                'id' => $material->getId(),
                'name' => $material->getName(),
                'amount' => $material->getAmount(),
                'type' => $material->getType()
            ];
        }, $materials);
    }

    public function getAllMaterialsWithStatus(): array
    {
        $materials = $this->materialRepository->findAll();
        $result = [];

        foreach ($materials as $material) {
            $status = $this->materialStatusRepository->findOneBy(['materialId' => $material->getId()]);
            $result[] = [
                'material' => [
                    'id' => $material->getId(),
                    'name' => $material->getName(),
                    'amount' => $material->getAmount(),
                    'type' => $material->getType()
                ],
                'status' => $status ? [
                    'status' => $status->getStatus(),
                    'current_point_id' => $status->getCurrentPointId(),
                    'updated_at' => $status->getUpdatedAt()->format('Y-m-d H:i:s')
                ] : null
            ];
        }

        return $result;
    }
}