<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\MaterialService;
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
            $params = $request->getQueryParams();
            
            // Если установлен top_level=1, игнорируем все остальные параметры и ищем только материалы без родителя
            if (isset($params['top_level']) && $params['top_level'] === '1') {
                $materials = $this->materialService->searchMaterials(['parent_id' => null]);
            } else if (!empty($params)) {
                $materials = $this->materialService->searchMaterials($params);
            } else {
                $materials = $this->materialService->getAllMaterials();
            }
            
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

    public function assemble(Request $request, Response $response, string $id): Response
    {
        try {
            $data = $request->getParsedBody();
            if (!isset($data['child_ids']) || !is_array($data['child_ids'])) {
                throw new Exception('child_ids array is required');
            }
            
            $result = $this->materialService->assembleMaterial((int)$id, $data['child_ids']);
            return $this->jsonResponse($response, $result);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
} 