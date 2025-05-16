<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Service\MovementService;
use Exception;

class ScanResponse {
    public function __construct(
        public int $id,
        public int $material_id,
        public int $route_point_id,
        public int $scanned_by,
        public string $scanned_at,
        public bool $is_deviation,
        public string $note
    ) {}
}

class HistoryEventResponse {
    public function __construct(
        public int $id,
        public int $material_id,
        public int $route_point_id,
        public int $scanned_by,
        public string $scanned_at,
        public bool $is_deviation,
        public string $note
    ) {}
}

class HistoryResponse {
    /** @var HistoryEventResponse[] */
    public array $events;

    public function __construct(array $events) {
        $this->events = $events;
    }
}

class MovementController extends AbstractController
{
    public function __construct(
        private MovementService $movementService
    ) {}

    public function scan(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            if (!isset($data['material_id'], $data['route_point_id'], $data['scanned_by'])) {
                throw new Exception('Missing required parameters');
            }
            
            $event = $this->movementService->scanMaterial([
                'material_id' => (int)$data['material_id'],
                'route_point_id' => (int)$data['route_point_id'],
                'user_id' => (int)$data['scanned_by'],
                'note' => $data['note'] ?? ''
            ]);
            
            $responseData = new ScanResponse(
                $event->getId(),
                $event->getMaterialId(),
                $event->getRoutePointId(),
                $event->getScannedBy(),
                $event->getScannedAt()->format('Y-m-d\TH:i:s.u\Z'),
                $event->isDeviation(),
                $event->getNote()
            );
            
            return $this->jsonResponse($response, $responseData, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function history(Request $request, Response $response, string $materialId): Response
    {
        try {
            if (!is_numeric($materialId)) {
                throw new Exception('Material ID must be a number');
            }
            $materialId = (int)$materialId;
            $history = $this->movementService->getMovementHistory($materialId);
            $events = array_map(function($event) {
                return new HistoryEventResponse(
                    $event->getId(),
                    $event->getMaterialId(),
                    $event->getRoutePointId(),
                    $event->getScannedBy(),
                    $event->getScannedAt()->format('Y-m-d\TH:i:s.u\Z'),
                    $event->isDeviation(),
                    $event->getNote()
                );
            }, $history);
            
            $responseData = new HistoryResponse($events);
            return $this->jsonResponse($response, $responseData);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
}