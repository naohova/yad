<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity]
#[ORM\Table(name: 'material_processes')]
class MaterialProcess implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Material::class)]
    #[ORM\JoinColumn(name: 'material_id', referencedColumnName: 'id', nullable: true)]
    private ?Material $material = null;

    #[ORM\ManyToOne(targetEntity: Process::class)]
    #[ORM\JoinColumn(name: 'process_id', referencedColumnName: 'id', nullable: true)]
    private ?Process $process = null;

    #[ORM\ManyToOne(targetEntity: Employee::class)]
    #[ORM\JoinColumn(name: 'employee_id', referencedColumnName: 'id', nullable: true)]
    private ?Employee $employee = null;

    #[ORM\ManyToOne(targetEntity: Place::class)]
    #[ORM\JoinColumn(name: 'place_id', referencedColumnName: 'id', nullable: true)]
    private ?Place $place = null;

    #[ORM\Column(name: 'planned_start', type: 'datetime', nullable: true)]
    private ?\DateTime $plannedStart = null;

    #[ORM\Column(name: 'planned_end', type: 'datetime', nullable: true)]
    private ?\DateTime $plannedEnd = null;

    #[ORM\Column(name: 'fact_start', type: 'datetime', nullable: true)]
    private ?\DateTime $factStart = null;

    #[ORM\Column(name: 'fact_end', type: 'datetime', nullable: true)]
    private ?\DateTime $factEnd = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;
        return $this;
    }

    public function getProcess(): ?Process
    {
        return $this->process;
    }

    public function setProcess(?Process $process): self
    {
        $this->process = $process;
        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;
        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;
        return $this;
    }

    public function getPlannedStart(): ?\DateTime
    {
        return $this->plannedStart;
    }

    public function setPlannedStart(?\DateTime $plannedStart): self
    {
        $this->plannedStart = $plannedStart;
        return $this;
    }

    public function getPlannedEnd(): ?\DateTime
    {
        return $this->plannedEnd;
    }

    public function setPlannedEnd(?\DateTime $plannedEnd): self
    {
        $this->plannedEnd = $plannedEnd;
        return $this;
    }

    public function getFactStart(): ?\DateTime
    {
        return $this->factStart;
    }

    public function setFactStart(?\DateTime $factStart): self
    {
        $this->factStart = $factStart;
        return $this;
    }

    public function getFactEnd(): ?\DateTime
    {
        return $this->factEnd;
    }

    public function setFactEnd(?\DateTime $factEnd): self
    {
        $this->factEnd = $factEnd;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'material' => [
                'id' => $this->material?->getId(),
                'part_number' => $this->material?->getPartNumber()
            ],
            'process' => $this->process,
            'employee' => $this->employee,
            'place' => $this->place,
            'planned_start' => $this->plannedStart ? $this->plannedStart->format('Y-m-d H:i:s') : null,
            'planned_end' => $this->plannedEnd ? $this->plannedEnd->format('Y-m-d H:i:s') : null,
            'fact_start' => $this->factStart ? $this->factStart->format('Y-m-d H:i:s') : null,
            'fact_end' => $this->factEnd ? $this->factEnd->format('Y-m-d H:i:s') : null,
            'status' => $this->status,
            'notes' => $this->notes
        ];
    }
} 