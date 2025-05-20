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

class MaterialProcessController extends AbstractController
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager);
    }

    public function getAll(Request $request, Response $response): Response
    {
        $processes = $this->entityManager->getRepository(MaterialProcess::class)->findAll();
        return $this->jsonResponse($response, $processes);
    }

    public function getOne(Request $request, Response $response, array $args): Response
    {
        $process = $this->entityManager->find(MaterialProcess::class, $args['id']);
        if (!$process) {
            return $this->notFoundResponse($response);
        }
        return $this->jsonResponse($response, $process);
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
            return $response->withStatus(400)
                ->withJson(['error' => 'One or more related entities not found']);
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

        $this->entityManager->persist($materialProcess);
        $this->entityManager->flush();

        return $this->jsonResponse($response, $materialProcess, 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $materialProcess = $this->entityManager->find(MaterialProcess::class, $args['id']);
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

        if (isset($data['end_time'])) {
            $materialProcess->setEndTime(new \DateTime($data['end_time']));
        }

        $this->entityManager->flush();

        return $this->jsonResponse($response, $materialProcess);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $materialProcess = $this->entityManager->find(MaterialProcess::class, $args['id']);
        if (!$materialProcess) {
            return $this->notFoundResponse($response);
        }

        $this->entityManager->remove($materialProcess);
        $this->entityManager->flush();

        return $response->withStatus(204);
    }

    public function getByMaterial(Request $request, Response $response, array $args): Response
    {
        $processes = $this->entityManager->getRepository(MaterialProcess::class)
            ->findBy(['material' => $args['material_id']]);
        return $this->jsonResponse($response, $processes);
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