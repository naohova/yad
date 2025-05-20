<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
#[ORM\Table(name: 'planned_routes')]
class PlannedRoute implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'material_id', type: 'integer')]
    private int $materialId;

    #[ORM\Column(name: 'route_point_id', type: 'integer')]
    private int $routePointId;

    #[ORM\Column(type: 'integer')]
    private int $sequence;

    #[ORM\Column(name: 'expected_at', type: 'datetime', nullable: true)]
    private ?\DateTime $expectedAt;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?\DateTime $deletedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = null;
        $this->deletedAt = null;
    }

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

    public function getSequence(): int
    {
        return $this->sequence;
    }

    public function setSequence(int $sequence): self
    {
        $this->sequence = $sequence;
        return $this;
    }

    public function getExpectedAt(): ?\DateTime
    {
        return $this->expectedAt;
    }

    public function setExpectedAt(?\DateTime $expectedAt): self
    {
        $this->expectedAt = $expectedAt;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'material_id' => $this->materialId,
            'route_point_id' => $this->routePointId,
            'sequence' => $this->sequence,
            'expected_at' => $this->expectedAt ? $this->expectedAt->format('Y-m-d\TH:i:s\Z') : null,
            'created_at' => $this->createdAt->format('Y-m-d\TH:i:s\Z'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d\TH:i:s\Z') : null,
            'deleted_at' => $this->deletedAt ? $this->deletedAt->format('Y-m-d\TH:i:s\Z') : null
        ];
    }
}