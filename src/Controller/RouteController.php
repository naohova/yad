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
            $point = $this->routePointService->createRoutePoint($data);
            return $this->jsonResponse($response, $point, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function planRoute(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $route = $this->plannedRouteService->createRoute($data);
            return $this->jsonResponse($response, $route, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function getMaterialRoute(Request $request, Response $response, int $materialId): Response
    {
        try {
            $route = $this->plannedRouteService->getMaterialRoute($materialId);
            return $this->jsonResponse($response, $route);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
} 