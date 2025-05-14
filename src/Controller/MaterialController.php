<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Service\MaterialService;
use Exception;

class MaterialController extends AbstractController
{
    public function __construct(
        private MaterialService $materialService
    ) {}

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $material = $this->materialService->createMaterial($data);
            return $this->jsonResponse($response, $material, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function list(Request $request, Response $response): Response
    {
        try {
            $materials = $this->materialService->getAllMaterials();
            return $this->jsonResponse($response, $materials);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function get(Request $request, Response $response, string $id): Response
    {
        try {
            $material = $this->materialService->getMaterialWithStatus((int)$id);
            return $this->jsonResponse($response, $material);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage(), 404);
        }
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        try {
            $data = $request->getParsedBody();
            $material = $this->materialService->updateMaterial((int)$id, $data);
            return $this->jsonResponse($response, $material);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response, string $id): Response
    {
        try {
            $this->materialService->deleteMaterial((int)$id);
            return $this->jsonResponse($response, ['message' => 'Material deleted']);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
} 