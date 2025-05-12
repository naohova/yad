<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'movement_events')]
class MovementEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'material_id', type: 'integer')]
    private int $materialId;

    #[ORM\Column(name: 'route_point_id', type: 'integer')]
    private int $routePointId;

    #[ORM\Column(name: 'scanned_by', type: 'integer')]
    private int $scannedBy;

    #[ORM\Column(name: 'scanned_at', type: 'datetime')]
    private string $scannedAt;

    #[ORM\Column(name: 'is_deviation', type: 'boolean')]
    private bool $isDeviation;

    #[ORM\Column(type: 'string')]
    private string $note;

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

    public function getRoutePointId(): int
    {
        return $this->routePointId;
    }

    public function setRoutePointId(int $routePointId): self
    {
        $this->routePointId = $routePointId;
        return $this;
    }

    public function getScannedBy(): int
    {
        return $this->scannedBy;
    }

    public function setScannedBy(int $scannedBy): self
    {
        $this->scannedBy = $scannedBy;
        return $this;
    }

    public function getScannedAt(): string
    {
        return $this->scannedAt;
    }

    public function setScannedAt(string $scannedAt): self
    {
        $this->scannedAt = $scannedAt;
        return $this;
    }

    public function isDeviation(): bool
    {
        return $this->isDeviation;
    }

    public function setIsDeviation(bool $isDeviation): self
    {
        $this->isDeviation = $isDeviation;
        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;
        return $this;
    }
}