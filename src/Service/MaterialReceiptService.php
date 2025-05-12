<?php

namespace Service;

use Entity\MaterialReceipt;
use Repository\MaterialReceiptRepository;
use Repository\MaterialRepository;
use Repository\UserRepository;
use Exception;

class MaterialReceiptService
{
    public function __construct(
        private MaterialReceiptRepository $materialReceiptRepository,
        private MaterialRepository $materialRepository,
        private UserRepository $userRepository
    ) {}

    public function createReceipt(array $data): MaterialReceipt
    {
        // Проверяем существование материала
        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        // Проверяем существование пользователя
        $user = $this->userRepository->find($data['received_by']);
        if (!$user) {
            throw new Exception('User not found');
        }

        $receipt = new MaterialReceipt();
        $receipt->setMaterialId($material->getId());
        $receipt->setReceivedBy($user->getId());
        $receipt->setSupplierName($data['supplier_name']);
        $receipt->setReceivedAt(date('Y-m-d H:i:s'));

        $this->materialReceiptRepository->save($receipt);
        return $receipt;
    }

    public function getReceiptsByMaterial(int $materialId): array
    {
        return $this->materialReceiptRepository->findBy(['materialId' => $materialId]);
    }
}