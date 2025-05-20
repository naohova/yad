<?php

namespace App\Service;

use App\Entity\Process;
use App\Entity\Employee;
use App\Entity\Place;
use Doctrine\ORM\EntityManager;
use App\Validator\ProcessValidator;
use Exception;

class ProcessService
{
    public function __construct(
        private EntityManager $entityManager,
        private ProcessValidator $validator
    ) {}

    public function createProcess(array $data): Process
    {
        $this->validator->validateCreate($data);

        $responsible = $this->entityManager->find(Employee::class, $data['responsible_id']);
        if (!$responsible) {
            throw new Exception('Responsible employee not found');
        }

        $place = $this->entityManager->find(Place::class, $data['place_id']);
        if (!$place) {
            throw new Exception('Place not found');
        }

        $process = new Process();
        $process->setName($data['name']);
        $process->setDescription($data['description'] ?? null);
        $process->setResponsible($responsible);
        $process->setPlace($place);

        $this->entityManager->persist($process);
        $this->entityManager->flush();

        return $process;
    }

    public function getProcess(int $id): ?Process
    {
        return $this->entityManager->find(Process::class, $id);
    }

    public function getAllProcesses(): array
    {
        return $this->entityManager->getRepository(Process::class)->findAll();
    }

    public function updateProcess(int $id, array $data): ?Process
    {
        $process = $this->getProcess($id);
        if (!$process) {
            return null;
        }

        $this->validator->validateUpdate($data);

        if (isset($data['name'])) {
            $process->setName($data['name']);
        }
        if (isset($data['description'])) {
            $process->setDescription($data['description']);
        }
        if (isset($data['responsible_id'])) {
            $responsible = $this->entityManager->find(Employee::class, $data['responsible_id']);
            if (!$responsible) {
                throw new Exception('Responsible employee not found');
            }
            $process->setResponsible($responsible);
        }
        if (isset($data['place_id'])) {
            $place = $this->entityManager->find(Place::class, $data['place_id']);
            if (!$place) {
                throw new Exception('Place not found');
            }
            $process->setPlace($place);
        }

        $this->entityManager->flush();

        return $process;
    }

    public function deleteProcess(int $id): bool
    {
        $process = $this->getProcess($id);
        if (!$process) {
            return false;
        }

        $this->entityManager->remove($process);
        $this->entityManager->flush();

        return true;
    }
} 