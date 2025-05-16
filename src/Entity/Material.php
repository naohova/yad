<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'materials')]
#[ORM\HasLifecycleCallbacks]
class Material
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'rfid_tag_id', type: 'integer', nullable: true)]
    private ?int $rfidTagId = null;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\OneToOne(targetEntity: MaterialStatus::class, mappedBy: 'material')]
    private ?MaterialStatus $status = null;

    #[ORM\Column(name: 'deleted_at', type: 'datetime', nullable: true)]
    private ?\DateTime $deletedAt = null;

    #[ORM\Column(name: 'parent_id', type: 'integer', nullable: true)]
    private ?int $parentId = null;

    #[ORM\Column(name: 'part_number', type: 'string', length: 255, nullable: true)]
    private ?string $partNumber = null;

    #[ORM\Column(name: 'last_route_point_id', type: 'integer', nullable: true)]
    private ?int $lastRoutePointId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTime $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue()
    {
        $this->updatedAt = new \DateTime();
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

    public function getRfidTagId(): ?int
    {
        return $this->rfidTagId;
    }

    public function setRfidTagId(?int $rfidTagId): self
    {
        $this->rfidTagId = $rfidTagId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): ?MaterialStatus
    {
        return $this->status;
    }

    public function setStatus(?MaterialStatus $status): self
    {
        $this->status = $status;
        if ($status !== null) {
            $status->setMaterial($this);
        }
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

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function getPartNumber(): ?string
    {
        return $this->partNumber;
    }

    public function setPartNumber(?string $partNumber): self
    {
        $this->partNumber = $partNumber;
        return $this;
    }

    public function getLastRoutePointId(): ?int
    {
        return $this->lastRoutePointId;
    }

    public function setLastRoutePointId(?int $lastRoutePointId): self
    {
        $this->lastRoutePointId = $lastRoutePointId;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}