<?php

namespace Service;

use Entity\Material;
use Entity\MaterialStatus;
use Repository\MaterialRepository;
use Repository\RfidTagRepository;
use Repository\MaterialStatusRepository;
use Exception;

class MaterialService
{
    public function __construct(
        private MaterialRepository $materialRepository,
        private RfidTagRepository $rfidTagRepository,
        private MaterialStatusRepository $materialStatusRepository
    ) {}

    public function createMaterial(array $data): Material
    {
        // Проверяем, что RFID метка существует и активна
        $rfidTag = $this->rfidTagRepository->findByTagUid($data['rfid_tag']);
        if (!$rfidTag || !$rfidTag->isActive()) {
            throw new Exception('Invalid or inactive RFID tag');
        }

        $material = new Material();
        $material->setName($data['name']);
        $material->setAmount($data['amount']);
        $material->setType($data['type']);
        $material->setRfidTagId($rfidTag->getId());

        $this->materialRepository->beginTransaction();
        try {
            $this->materialRepository->save($material);
            
            // Создаем начальный статус для материала
            $status = new MaterialStatus();
            $status->setMaterialId($material->getId());
            $status->setStatus('created');
            $status->setCurrentPointId($data['initial_point_id']);
            $status->setUpdatedAt(date('Y-m-d H:i:s'));
            
            $this->materialStatusRepository->save($status);
            
            $this->materialRepository->commit();
            return $material;
        } catch (Exception $e) {
            $this->materialRepository->rollback();
            throw $e;
        }
    }

    public function updateMaterial(int $id, array $data): Material
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
        return $material;
    }

    public function getMaterialWithStatus(int $id): array
    {
        $material = $this->materialRepository->find($id);
        if (!$material) {
            throw new Exception('Material not found');
        }

        $status = $this->materialStatusRepository->findOneBy(['materialId' => $id]);
        
        return [
            'material' => $material,
            'status' => $status
        ];
    }

    public function getAllMaterials(): array
    {
        return $this->materialRepository->findAll();
    }

    public function getAllMaterialsWithStatus(): array
    {
        $materials = $this->materialRepository->findAll();
        $result = [];

        foreach ($materials as $material) {
            $status = $this->materialStatusRepository->findOneBy(['materialId' => $material->getId()]);
            $result[] = [
                'material' => $material,
                'status' => $status
            ];
        }

        return $result;
    }
}