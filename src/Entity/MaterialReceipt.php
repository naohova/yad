<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_receipts')]
class MaterialReceipt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'material_id', type: 'integer')]
    private int $materialId;

    #[ORM\Column(name: 'received_by', type: 'integer')]
    private int $receivedBy;

    #[ORM\Column(name: 'supplier_name', type: 'string')]
    private string $supplierName;

    #[ORM\Column(name: 'received_at', type: 'datetime')]
    private \DateTime $receivedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    public function setMaterialId(int $materialId): self
    {
        $this->materialId = $materialId;
        return $this;
    }

    public function getReceivedBy(): int
    {
        return $this->receivedBy;
    }

    public function setReceivedBy(int $receivedBy): self
    {
        $this->receivedBy = $receivedBy;
        return $this;
    }

    public function getSupplierName(): string
    {
        return $this->supplierName;
    }

    public function setSupplierName(string $supplierName): self
    {
        $this->supplierName = $supplierName;
        return $this;
    }

    public function getReceivedAt(): \DateTime
    {
        return $this->receivedAt;
    }

    public function setReceivedAt(\DateTime $receivedAt): self
    {
        $this->receivedAt = $receivedAt;
        return $this;
    }
}