<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Service\PlannedRouteService;
use Service\RoutePointService;
use Exception;

class RouteController extends AbstractController
{
    public function __construct(
        private PlannedRouteService $plannedRouteService,
        private RoutePointService $routePointService
    ) {}

    public function createPoint(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            if (!isset($data['name'], $data['type'])) {
                throw new Exception('Missing required parameters');
            }
            
            $point = $this->routePointService->createRoutePoint($data);
            return $this->jsonResponse($response, [
                'id' => $point->getId(),
                'name' => $point->getName(),
                'type' => $point->getType()
            ], 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function listPoints(Request $request, Response $response): Response
    {
        try {
            $points = $this->routePointService->getAllPoints();
            return $this->jsonResponse($response, ['points' => $points]);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function getPoint(Request $request, Response $response, string $id): Response
    {
        try {
            $point = $this->routePointService->getRoutePoint((int)$id);
            return $this->jsonResponse($response, $point);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function updatePoint(Request $request, Response $response, string $id): Response
    {
        try {
            $data = $request->getParsedBody();
            $point = $this->routePointService->updateRoutePoint((int)$id, $data);
            return $this->jsonResponse($response, [
                'id' => $point->getId(),
                'name' => $point->getName(),
                'type' => $point->getType()
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function deletePoint(Request $request, Response $response, string $id): Response
    {
        try {
            $this->routePointService->deletePoint((int)$id);
            return $this->jsonResponse($response, ['message' => 'Route point deleted successfully'], 200);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function planRoute(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            if (!is_array($data)) {
                error_log('Invalid request body: ' . var_export($data, true));
                throw new Exception('Invalid request body');
            }

            error_log('Request data: ' . json_encode($data, JSON_PRETTY_PRINT));
            
            if (!isset($data['material_id'])) {
                error_log('Missing material_id in request');
                throw new Exception('Missing material_id');
            }

            if (!isset($data['route_points']) || !is_array($data['route_points'])) {
                error_log('Missing or invalid route_points in request');
                throw new Exception('Missing or invalid route_points');
            }

            $routes = $this->plannedRouteService->createRoute($data);
            error_log('Routes created successfully: ' . json_encode($routes, JSON_PRETTY_PRINT));
            return $this->jsonResponse($response, $routes, 201);
        } catch (Exception $e) {
            error_log('Error in planRoute: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function getMaterialRoute(Request $request, Response $response, string $materialId): Response
    {
        try {
            $routes = $this->plannedRouteService->getMaterialRoute((int)$materialId);
            return $this->jsonResponse($response, $routes, 200);
        } catch (Exception $e) {
            error_log('Error in getMaterialRoute: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            return $this->jsonResponse($response, ['error' => $e->getMessage()], 400);
        }
    }
} 