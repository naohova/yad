<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'rfid_tags')]
class RfidTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $tag;

    #[ORM\Column(name: 'material_id', type: 'integer', nullable: true)]
    private ?int $materialId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime')]
    private \DateTime $updatedAt;

    #[ORM\Column(name: 'tag_uid', type: 'string')]
    private string $tagUid;

    #[ORM\Column(name: 'is_active', type: 'boolean')]
    private bool $isActive;

    #[ORM\Column(name: 'assigned_at', type: 'datetime')]
    private \DateTime $assignedAt;

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

    public function getTagUid(): string
    {
        return $this->tagUid;
    }

    public function setTagUid(string $tagUid): self
    {
        $this->tagUid = $tagUid;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getAssignedAt(): \DateTime
    {
        return $this->assignedAt;
    }

    public function setAssignedAt(\DateTime $assignedAt): self
    {
        $this->assignedAt = $assignedAt;
        return $this;
    }
}