<?php

namespace App\Controller;

use App\Entity\MaterialProcess;
use App\Entity\Material;
use App\Entity\Process;
use App\Entity\Employee;
use App\Entity\Place;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\ORM\EntityManager;
use Exception;
use App\Service\MaterialProcessService;

class MaterialProcessController extends AbstractController
{
    private MaterialProcessService $materialProcessService;

    public function __construct(EntityManager $entityManager, MaterialProcessService $materialProcessService)
    {
        parent::__construct($entityManager);
        $this->materialProcessService = $materialProcessService;
    }

    private function formatMaterialProcess(MaterialProcess $process): array
    {
        $material = $process->getMaterial();
        return [
            'id' => $process->getId(),
            'material' => [
                'id' => $material?->getId(),
                'part_number' => $material?->getPartNumber()
            ],
            'process' => $process->getProcess(),
            'employee' => $process->getEmployee(),
            'place' => $process->getPlace(),
            'planned_start' => $process->getPlannedStart() ? $process->getPlannedStart()->format('Y-m-d H:i:s') : null,
            'planned_end' => $process->getPlannedEnd() ? $process->getPlannedEnd()->format('Y-m-d H:i:s') : null,
            'fact_start' => $process->getFactStart() ? $process->getFactStart()->format('Y-m-d H:i:s') : null,
            'fact_end' => $process->getFactEnd() ? $process->getFactEnd()->format('Y-m-d H:i:s') : null,
            'status' => $process->getStatus(),
            'notes' => $process->getNotes()
        ];
    }

    public function getAll(Request $request, Response $response): Response
    {
        try {
            $processes = $this->entityManager->getRepository(MaterialProcess::class)->findAll();
            $result = array_map([$this, 'formatMaterialProcess'], $processes);
            return $this->jsonResponse($response, $result);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function getOne(Request $request, Response $response, string $id): Response
    {
        try {
            $process = $this->entityManager->find(MaterialProcess::class, (int)$id);
            if (!$process) {
                return $this->notFoundResponse($response);
            }
            return $this->jsonResponse($response, $this->formatMaterialProcess($process));
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Проверяем существование связанных сущностей
        $material = $this->entityManager->find(Material::class, $data['material_id']);
        $process = $this->entityManager->find(Process::class, $data['process_id']);
        $employee = $this->entityManager->find(Employee::class, $data['employee_id']);
        $place = $this->entityManager->find(Place::class, $data['place_id']);

        if (!$material || !$process || !$employee || !$place) {
            return $this->errorResponse($response, 'One or more related entities not found', 400);
        }

        $materialProcess = new MaterialProcess();
        $materialProcess->setMaterial($material);
        $materialProcess->setProcess($process);
        $materialProcess->setEmployee($employee);
        $materialProcess->setPlace($place);
        $materialProcess->setStatus($data['status']);
        $materialProcess->setNotes($data['notes'] ?? null);
        
        if (isset($data['planned_start'])) {
            $materialProcess->setPlannedStart(new \DateTime($data['planned_start']));
        }
        if (isset($data['planned_end'])) {
            $materialProcess->setPlannedEnd(new \DateTime($data['planned_end']));
        }
        if (isset($data['fact_start'])) {
            $materialProcess->setFactStart(new \DateTime($data['fact_start']));
        }
        if (isset($data['fact_end'])) {
            $materialProcess->setFactEnd(new \DateTime($data['fact_end']));
        }

        $this->entityManager->persist($materialProcess);
        $this->entityManager->flush();

        return $this->jsonResponse($response, $this->formatMaterialProcess($materialProcess), 201);
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        $materialProcess = $this->entityManager->find(MaterialProcess::class, (int)$id);
        if (!$materialProcess) {
            return $this->notFoundResponse($response);
        }

        $data = $request->getParsedBody();
        
        if (isset($data['material_id'])) {
            $material = $this->entityManager->find(Material::class, $data['material_id']);
            if ($material) {
                $materialProcess->setMaterial($material);
            }
        }

        if (isset($data['process_id'])) {
            $process = $this->entityManager->find(Process::class, $data['process_id']);
            if ($process) {
                $materialProcess->setProcess($process);
            }
        }

        if (isset($data['employee_id'])) {
            $employee = $this->entityManager->find(Employee::class, $data['employee_id']);
            if ($employee) {
                $materialProcess->setEmployee($employee);
            }
        }

        if (isset($data['place_id'])) {
            $place = $this->entityManager->find(Place::class, $data['place_id']);
            if ($place) {
                $materialProcess->setPlace($place);
            }
        }

        if (isset($data['status'])) {
            $materialProcess->setStatus($data['status']);
        }

        if (isset($data['notes'])) {
            $materialProcess->setNotes($data['notes']);
        }

        if (isset($data['planned_start'])) {
            $materialProcess->setPlannedStart(new \DateTime($data['planned_start']));
        }
        if (isset($data['planned_end'])) {
            $materialProcess->setPlannedEnd(new \DateTime($data['planned_end']));
        }
        if (isset($data['fact_start'])) {
            $materialProcess->setFactStart(new \DateTime($data['fact_start']));
        }
        if (isset($data['fact_end'])) {
            $materialProcess->setFactEnd(new \DateTime($data['fact_end']));
        }

        $this->entityManager->flush();

        return $this->jsonResponse($response, $this->formatMaterialProcess($materialProcess));
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        try {
            $this->materialProcessService->deleteMaterialProcess((int)$args['id']);
            return $this->jsonResponse($response, ['message' => 'Material process deleted']);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function getByMaterial(Request $request, Response $response): Response
    {
        $materialId = (int) $request->getAttribute('id');
        try {
            $processes = $this->materialProcessService->getByMaterial($materialId);
            $result = array_map([$this, 'formatMaterialProcess'], $processes);
            return $this->jsonResponse($response, $result);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function getByEmployee(Request $request, Response $response, array $args): Response
    {
        $processes = $this->entityManager->getRepository(MaterialProcess::class)
            ->findBy(['employee' => $args['employee_id']]);
        return $this->jsonResponse($response, $processes);
    }

    public function getByPlace(Request $request, Response $response, array $args): Response
    {
        $processes = $this->entityManager->getRepository(MaterialProcess::class)
            ->findBy(['place' => $args['place_id']]);
        return $this->jsonResponse($response, $processes);
    }
} 