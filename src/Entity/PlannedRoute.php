<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'planned_routes')]
class PlannedRoute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'material_id', type: 'integer')]
    private int $materialId;

    #[ORM\Column(name: 'route_point_id', type: 'integer')]
    private int $routePointId;

    #[ORM\Column(type: 'float')]
    private float $sequence;

    #[ORM\Column(name: 'expected_at', type: 'datetime', nullable: true)]
    private ?string $expectedAt;

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

    public function getSequence(): float
    {
        return $this->sequence;
    }

    public function setSequence(float $sequence): self
    {
        $this->sequence = $sequence;
        return $this;
    }

    public function getExpectedAt(): ?string
    {
        return $this->expectedAt;
    }

    public function setExpectedAt(?string $expectedAt): self
    {
        $this->expectedAt = $expectedAt;
        return $this;
    }
}