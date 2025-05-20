<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
#[ORM\Table(name: 'routes')]
class Route implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Material::class)]
    #[ORM\JoinColumn(name: 'material_id', referencedColumnName: 'id', nullable: false)]
    private Material $material;

    #[ORM\ManyToOne(targetEntity: RoutePoint::class)]
    #[ORM\JoinColumn(name: 'route_point_id', referencedColumnName: 'id', nullable: false)]
    private RoutePoint $routePoint;

    #[ORM\Column(name: 'planned_start_date', type: 'datetime', nullable: true)]
    private ?\DateTime $plannedStartDate;

    #[ORM\Column(name: 'planned_end_date', type: 'datetime', nullable: true)]
    private ?\DateTime $plannedEndDate;

    #[ORM\Column(name: 'actual_start_date', type: 'datetime', nullable: true)]
    private ?\DateTime $actualStartDate;

    #[ORM\Column(name: 'actual_end_date', type: 'datetime', nullable: true)]
    private ?\DateTime $actualEndDate;

    #[ORM\Column(name: 'delay_reason', type: 'string', length: 255, nullable: true)]
    private ?string $delayReason;

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
        $this->delayReason = null;
        $this->plannedStartDate = null;
        $this->plannedEndDate = null;
        $this->actualStartDate = null;
        $this->actualEndDate = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMaterial(): Material
    {
        return $this->material;
    }

    public function setMaterial(Material $material): self
    {
        $this->material = $material;
        return $this;
    }

    public function getRoutePoint(): RoutePoint
    {
        return $this->routePoint;
    }

    public function setRoutePoint(RoutePoint $routePoint): self
    {
        $this->routePoint = $routePoint;
        return $this;
    }

    public function getPlannedStartDate(): ?\DateTime
    {
        return $this->plannedStartDate;
    }

    public function setPlannedStartDate(?\DateTime $plannedStartDate): self
    {
        $this->plannedStartDate = $plannedStartDate;
        return $this;
    }

    public function getPlannedEndDate(): ?\DateTime
    {
        return $this->plannedEndDate;
    }

    public function setPlannedEndDate(?\DateTime $plannedEndDate): self
    {
        $this->plannedEndDate = $plannedEndDate;
        return $this;
    }

    public function getActualStartDate(): ?\DateTime
    {
        return $this->actualStartDate;
    }

    public function setActualStartDate(?\DateTime $actualStartDate): self
    {
        $this->actualStartDate = $actualStartDate;
        return $this;
    }

    public function getActualEndDate(): ?\DateTime
    {
        return $this->actualEndDate;
    }

    public function setActualEndDate(?\DateTime $actualEndDate): self
    {
        $this->actualEndDate = $actualEndDate;
        return $this;
    }

    public function getDelayReason(): ?string
    {
        return $this->delayReason;
    }

    public function setDelayReason(?string $delayReason): self
    {
        $this->delayReason = $delayReason;
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
            'material' => $this->material,
            'route_point' => $this->routePoint,
            'planned_start_date' => $this->plannedStartDate ? $this->plannedStartDate->format('Y-m-d\TH:i:s\Z') : null,
            'planned_end_date' => $this->plannedEndDate ? $this->plannedEndDate->format('Y-m-d\TH:i:s\Z') : null,
            'actual_start_date' => $this->actualStartDate ? $this->actualStartDate->format('Y-m-d\TH:i:s\Z') : null,
            'actual_end_date' => $this->actualEndDate ? $this->actualEndDate->format('Y-m-d\TH:i:s\Z') : null,
            'delay_reason' => $this->delayReason,
            'created_at' => $this->createdAt->format('Y-m-d\TH:i:s\Z'),
            'updated_at' => $this->updatedAt ? $this->updatedAt->format('Y-m-d\TH:i:s\Z') : null,
            'deleted_at' => $this->deletedAt ? $this->deletedAt->format('Y-m-d\TH:i:s\Z') : null
        ];
    }
} 