<?php

namespace App\Service;

use App\Entity\Process;
use Doctrine\ORM\EntityRepository;
use App\Validator\ProcessValidator;
use Exception;

class ProcessService
{
    public function __construct(
        private EntityRepository $processRepository,
        private ProcessValidator $validator
    ) {}

    public function createProcess(array $data): Process
    {
        $this->validator->validateCreate($data);

        $process = new Process();
        $process->setName($data['name']);
        $process->setDescription($data['description'] ?? null);
        $process->setDurationMinutes($data['duration_minutes'] ?? null);

        $this->processRepository->save($process);
        return $process;
    }

    public function getProcess(int $id): Process
    {
        $process = $this->processRepository->find($id);
        if (!$process) {
            throw new Exception('Process not found');
        }
        return $process;
    }

    public function getAllProcesses(): array
    {
        return $this->processRepository->findAll();
    }

    public function updateProcess(int $id, array $data): Process
    {
        $this->validator->validateUpdate($data);

        $process = $this->processRepository->find($id);
        if (!$process) {
            throw new Exception('Process not found');
        }

        if (isset($data['name'])) {
            $process->setName($data['name']);
        }
        if (isset($data['description'])) {
            $process->setDescription($data['description']);
        }
        if (isset($data['duration_minutes'])) {
            $process->setDurationMinutes($data['duration_minutes']);
        }

        $this->processRepository->save($process);
        return $process;
    }

    public function deleteProcess(int $id): void
    {
        $process = $this->processRepository->find($id);
        if (!$process) {
            throw new Exception('Process not found');
        }

        $this->processRepository->remove($process);
    }
} 