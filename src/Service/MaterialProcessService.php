<?php

namespace App\Service;

use App\Entity\MaterialProcess;
use App\Entity\Material;
use App\Entity\Process;
use App\Entity\Employee;
use App\Entity\Place;
use Doctrine\ORM\EntityRepository;
use App\Validator\MaterialProcessValidator;
use Exception;

class MaterialProcessService
{
    public function __construct(
        private EntityRepository $materialProcessRepository,
        private EntityRepository $materialRepository,
        private EntityRepository $processRepository,
        private EntityRepository $employeeRepository,
        private EntityRepository $placeRepository,
        private MaterialProcessValidator $validator
    ) {}

    public function createMaterialProcess(array $data): MaterialProcess
    {
        $this->validator->validateCreate($data);

        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        $process = $this->processRepository->find($data['process_id']);
        if (!$process) {
            throw new Exception('Process not found');
        }

        $employee = $this->employeeRepository->find($data['employee_id']);
        if (!$employee) {
            throw new Exception('Employee not found');
        }

        $place = $this->placeRepository->find($data['place_id']);
        if (!$place) {
            throw new Exception('Place not found');
        }

        $materialProcess = new MaterialProcess();
        $materialProcess->setMaterial($material);
        $materialProcess->setProcess($process);
        $materialProcess->setEmployee($employee);
        $materialProcess->setPlace($place);
        $materialProcess->setStatus($data['status']);
        $materialProcess->setNotes($data['notes'] ?? null);

        if (isset($data['end_time'])) {
            $materialProcess->setEndTime(new \DateTime($data['end_time']));
        }

        $this->materialProcessRepository->save($materialProcess);
        return $materialProcess;
    }

    public function getMaterialProcess(int $id): MaterialProcess
    {
        $materialProcess = $this->materialProcessRepository->find($id);
        if (!$materialProcess) {
            throw new Exception('Material process not found');
        }
        return $materialProcess;
    }

    public function getAllMaterialProcesses(): array
    {
        return $this->materialProcessRepository->findAll();
    }

    public function getMaterialProcessesByMaterial(int $materialId): array
    {
        return $this->materialProcessRepository->findBy(['material' => $materialId]);
    }

    public function getMaterialProcessesByEmployee(int $employeeId): array
    {
        return $this->materialProcessRepository->findBy(['employee' => $employeeId]);
    }

    public function getMaterialProcessesByPlace(int $placeId): array
    {
        return $this->materialProcessRepository->findBy(['place' => $placeId]);
    }

    public function updateMaterialProcess(int $id, array $data): MaterialProcess
    {
        $this->validator->validateUpdate($data);

        $materialProcess = $this->materialProcessRepository->find($id);
        if (!$materialProcess) {
            throw new Exception('Material process not found');
        }

        if (isset($data['material_id'])) {
            $material = $this->materialRepository->find($data['material_id']);
            if (!$material) {
                throw new Exception('Material not found');
            }
            $materialProcess->setMaterial($material);
        }

        if (isset($data['process_id'])) {
            $process = $this->processRepository->find($data['process_id']);
            if (!$process) {
                throw new Exception('Process not found');
            }
            $materialProcess->setProcess($process);
        }

        if (isset($data['employee_id'])) {
            $employee = $this->employeeRepository->find($data['employee_id']);
            if (!$employee) {
                throw new Exception('Employee not found');
            }
            $materialProcess->setEmployee($employee);
        }

        if (isset($data['place_id'])) {
            $place = $this->placeRepository->find($data['place_id']);
            if (!$place) {
                throw new Exception('Place not found');
            }
            $materialProcess->setPlace($place);
        }

        if (isset($data['status'])) {
            $materialProcess->setStatus($data['status']);
        }

        if (isset($data['notes'])) {
            $materialProcess->setNotes($data['notes']);
        }

        if (isset($data['end_time'])) {
            $materialProcess->setEndTime(new \DateTime($data['end_time']));
        }

        $this->materialProcessRepository->save($materialProcess);
        return $materialProcess;
    }

    public function deleteMaterialProcess(int $id): void
    {
        $materialProcess = $this->materialProcessRepository->find($id);
        if (!$materialProcess) {
            throw new Exception('Material process not found');
        }

        $this->materialProcessRepository->remove($materialProcess);
    }
} 