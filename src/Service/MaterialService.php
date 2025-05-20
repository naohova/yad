<?php

namespace App\Service;

use App\Entity\Material;
use App\Entity\MaterialStatus;
use App\Repository\MaterialRepository;
use App\Repository\RfidTagRepository;
use App\Repository\MaterialStatusRepository;
use Exception;
use App\Validator\MaterialValidator;

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
            $status->setMaterial($material);
            $status->setStatus('created');
            $status->setCurrentPointId($data['initial_point_id']);
            $status->setUpdatedAt(new \DateTime());
            
            $this->materialStatusRepository->save($status);
            
            $this->materialRepository->commit();
            
            return $this->serializeMaterial($material);
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
        
        return $this->serializeMaterial($material);
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
        
        $result = $this->serializeMaterial($material);
        $result['status'] = [
            'status' => $status->getStatus(),
            'current_point_id' => $status->getCurrentPointId(),
            'updated_at' => $status->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        return $result;
    }

    public function deleteMaterial(int $id): void
    {
        $material = $this->materialRepository->find($id);
        if (!$material) {
            throw new Exception('Material not found');
        }

        // Вместо физического удаления, устанавливаем deleted_at
        $material->setDeletedAt(new \DateTime());
        $this->materialRepository->save($material);
    }

    public function assembleMaterial(int $parentId, array $childIds): array
    {
        $parent = $this->materialRepository->find($parentId);
        if (!$parent) {
            throw new Exception('Parent material not found');
        }

        $this->materialRepository->beginTransaction();
        try {
            foreach ($childIds as $childId) {
                $child = $this->materialRepository->find($childId);
                if (!$child) {
                    throw new Exception("Child material with id {$childId} not found");
                }
                if ($child->getDeletedAt() !== null) {
                    throw new Exception("Child material with id {$childId} is already deleted");
                }
                
                // Помечаем дочерний материал как удаленный и устанавливаем ссылку на родителя
                $child->setDeletedAt(new \DateTime());
                $child->setParentId($parentId);
                $this->materialRepository->save($child);
            }
            
            $this->materialRepository->commit();
            
            return [
                'id' => $parent->getId(),
                'name' => $parent->getName(),
                'amount' => $parent->getAmount(),
                'type' => $parent->getType(),
                'assembled_from' => $childIds
            ];
        } catch (Exception $e) {
            $this->materialRepository->rollback();
            throw $e;
        }
    }

    public function getAllMaterials(): array
    {
        $materials = $this->materialRepository->findAll();
        return array_map(function($material) {
            return $this->serializeMaterial($material);
        }, $materials);
    }

    public function searchMaterials(array $params): array
    {
        $materials = $this->materialRepository->findByParams($params);
        return array_map(function($material) {
            return $this->serializeMaterial($material);
        }, $materials);
    }

    private function serializeMaterial(Material $material): array
    {
        $data = [
            'id' => $material->getId(),
            'name' => $material->getName(),
            'amount' => $material->getAmount(),
            'type' => $material->getType(),
            'part_number' => $material->getPartNumber(),
            'last_route_point_id' => $material->getLastRoutePointId(),
            'created_at' => $material->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z'),
            'updated_at' => $material->getUpdatedAt()->format('Y-m-d\TH:i:s.u\Z')
        ];

        if ($material->getDeletedAt() !== null) {
            $data['deleted_at'] = $material->getDeletedAt()->format('Y-m-d\TH:i:s.u\Z');
        }

        if ($material->getParentId() !== null) {
            $data['parent_id'] = $material->getParentId();
        }

        return $data;
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