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

    #[ORM\Column(type: 'integer')]
    private int $sequence;

    #[ORM\Column(name: 'expected_at', type: 'datetime', nullable: true)]
    private ?\DateTime $expectedAt = null;

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

    public function setExpectedAt(?string $expectedAt): self
    {
        if ($expectedAt === null) {
            $this->expectedAt = null;
        } else {
            try {
                $this->expectedAt = new \DateTime($expectedAt);
            } catch (\Exception $e) {
                throw new \InvalidArgumentException('Invalid date format for expected_at: ' . $expectedAt);
            }
        }
        return $this;
    }
}