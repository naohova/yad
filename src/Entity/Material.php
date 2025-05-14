<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'materials')]
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
}