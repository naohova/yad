<?php

namespace App\Service;

use App\Entity\MaterialReceipt;
use App\Repository\MaterialReceiptRepository;
use App\Repository\MaterialRepository;
use App\Repository\UserRepository;
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
        $receipt->setReceivedAt((new \DateTime())->format('Y-m-d H:i:s'));

        $this->materialReceiptRepository->save($receipt);
        return $receipt;
    }

    public function getReceiptsByMaterial(int $materialId): array
    {
        $receipts = $this->materialReceiptRepository->findBy(['materialId' => $materialId]);
        return array_map(function($receipt) {
            return [
                'id' => $receipt->getId(),
                'material_id' => $receipt->getMaterialId(),
                'received_by' => $receipt->getReceivedBy(),
                'supplier_name' => $receipt->getSupplierName(),
                'received_at' => $receipt->getReceivedAt()->format('Y-m-d H:i:s')
            ];
        }, $receipts);
    }
}