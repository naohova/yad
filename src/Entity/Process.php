<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
#[ORM\Table(name: 'processes')]
class Process implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'responsible_id', referencedColumnName: 'id', nullable: false)]
    private Employee $responsible;

    #[ORM\ManyToOne(targetEntity: Place::class)]
    #[ORM\JoinColumn(name: 'place_id', referencedColumnName: 'id', nullable: false)]
    private Place $place;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getResponsible(): Employee
    {
        return $this->responsible;
    }

    public function setResponsible(Employee $responsible): self
    {
        $this->responsible = $responsible;
        return $this;
    }

    public function getPlace(): Place
    {
        return $this->place;
    }

    public function setPlace(Place $place): self
    {
        $this->place = $place;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'responsible' => $this->responsible,
            'place' => $this->place
        ];
    }
} 