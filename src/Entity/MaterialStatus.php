<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'material_statuses')]
class MaterialStatus
{
    #[ORM\Id]
    #[ORM\Column(name: 'material_id', type: 'integer')]
    private int $materialId;

    #[ORM\Column(name: 'current_point_id', type: 'integer')]
    private int $currentPointId;

    #[ORM\Column(type: 'string')]
    private string $status;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private string $updatedAt;

    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    public function setMaterialId(int $materialId): self
    {
        $this->materialId = $materialId;
        return $this;
    }

    public function getCurrentPointId(): int
    {
        return $this->currentPointId;
    }

    public function setCurrentPointId(int $currentPointId): self
    {
        $this->currentPointId = $currentPointId;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}