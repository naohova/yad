<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Service\MovementService;
use Exception;

class MovementController extends AbstractController
{
    public function __construct(
        private MovementService $movementService
    ) {}

    public function scan(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $event = $this->movementService->scanMaterial($data);
            return $this->jsonResponse($response, $event, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function history(Request $request, Response $response, int $materialId): Response
    {
        try {
            $history = $this->movementService->getMovementHistory($materialId);
            return $this->jsonResponse($response, $history);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
}