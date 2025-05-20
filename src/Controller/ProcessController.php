<?php

namespace App\Controller;

use App\Entity\Process;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\ORM\EntityManager;
use App\Service\ProcessService;

class ProcessController extends AbstractController
{
    private ProcessService $processService;

    public function __construct(EntityManager $entityManager, ProcessService $processService)
    {
        parent::__construct($entityManager);
        $this->processService = $processService;
    }

    public function list(Request $request, Response $response): Response
    {
        $processes = $this->processService->getAllProcesses();
        return $this->jsonResponse($response, $processes);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $process = $this->processService->getProcess((int)$args['id']);
        if (!$process) {
            return $this->notFoundResponse($response);
        }
        return $this->jsonResponse($response, $process);
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $process = $this->processService->createProcess($data);
        return $this->jsonResponse($response, $process, 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $process = $this->processService->updateProcess((int)$args['id'], $data);
        if (!$process) {
            return $this->notFoundResponse($response);
        }
        return $this->jsonResponse($response, $process);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $result = $this->processService->deleteProcess((int)$args['id']);
        if (!$result) {
            return $this->notFoundResponse($response);
        }
        return $response->withStatus(204);
    }
} 