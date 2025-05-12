<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\MaterialReceipt;

class MaterialReceiptRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, MaterialReceipt::class);
    }

    public function findBySupplier(string $supplierName): array
    {
        return $this->repository->findBy(['supplierName' => $supplierName]);
    }

    public function findByReceiver(int $receivedBy): array
    {
        return $this->repository->findBy(['receivedBy' => $receivedBy]);
    }
}