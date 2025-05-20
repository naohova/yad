<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'routes')]
class Route
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

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $plannedStartDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $plannedEndDate;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $actualDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $delayReason;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $deletedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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

    public function getActualDate(): ?\DateTime
    {
        return $this->actualDate;
    }

    public function setActualDate(?\DateTime $actualDate): self
    {
        $this->actualDate = $actualDate;
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

    public function setUpdatedAt(\DateTime $updatedAt): self
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
} 