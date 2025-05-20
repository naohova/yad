<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Service\RouteService;
use Exception;

class RouteController extends AbstractController
{
    public function __construct(
        private RouteService $routeService
    ) {}

    public function createPlannedRoute(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $route = $this->routeService->createPlannedRoute($data);
            return $this->jsonResponse($response, $route, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function createActualRoute(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $route = $this->routeService->createActualRoute($data);
            return $this->jsonResponse($response, $route, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function createPoint(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $point = $this->routeService->createRoutePoint($data);
            return $this->jsonResponse($response, $point, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function list(Request $request, Response $response): Response
    {
        try {
            $params = $request->getQueryParams();
            $routes = match (true) {
                isset($params['type']) && $params['type'] === 'planned' => $this->routeService->getPlannedRoutes(),
                isset($params['type']) && $params['type'] === 'completed' => $this->routeService->getCompletedRoutes(),
                isset($params['type']) && $params['type'] === 'delayed' => $this->routeService->getDelayedRoutes(),
                isset($params['material_id']) => $this->routeService->getMaterialRoutes((int)$params['material_id']),
                default => $this->routeService->getAllRoutes(),
            };
            return $this->jsonResponse($response, $routes);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function get(Request $request, Response $response, string $id): Response
    {
        try {
            $route = $this->routeService->getRoute((int)$id);
            return $this->jsonResponse($response, $route);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage(), 404);
        }
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        try {
            $data = $request->getParsedBody();
            $route = $this->routeService->updateRoute((int)$id, $data);
            return $this->jsonResponse($response, $route);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response, string $id): Response
    {
        try {
            $this->routeService->deleteRoute((int)$id);
            return $this->jsonResponse($response, ['message' => 'Route deleted']);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
} 