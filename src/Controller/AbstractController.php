<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Doctrine\ORM\EntityManager;

abstract class AbstractController
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function notFoundResponse(Response $response): Response
    {
        return $this->jsonResponse($response, ['error' => 'Not found'], 404);
    }

    protected function errorResponse(Response $response, string $message, int $status = 400): Response
    {
        return $this->jsonResponse($response, ['error' => $message], $status);
    }
}