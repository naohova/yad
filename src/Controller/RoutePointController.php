<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Service\RoutePointService;
use Exception;

class RoutePointController extends AbstractController
{
    public function __construct(
        private RoutePointService $routePointService
    ) {}

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $routePoint = $this->routePointService->createRoutePoint($data);
            return $this->jsonResponse($response, $routePoint, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function list(Request $request, Response $response): Response
    {
        try {
            $routePoints = $this->routePointService->getAllRoutePoints();
            return $this->jsonResponse($response, $routePoints);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function get(Request $request, Response $response, string $id): Response
    {
        try {
            $routePoint = $this->routePointService->getRoutePoint((int)$id);
            return $this->jsonResponse($response, $routePoint);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage(), 404);
        }
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        try {
            $data = $request->getParsedBody();
            $routePoint = $this->routePointService->updateRoutePoint((int)$id, $data);
            return $this->jsonResponse($response, $routePoint);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function delete(Request $request, Response $response, string $id): Response
    {
        try {
            $this->routePointService->deleteRoutePoint((int)$id);
            return $this->jsonResponse($response, ['message' => 'Route point deleted']);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
} 