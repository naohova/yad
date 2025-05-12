<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Exception;

abstract class AbstractController
{
    protected function jsonResponse(Response $response, $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }

    protected function errorResponse(Response $response, string $message, int $status = 400): Response
    {
        return $this->jsonResponse($response, ['error' => $message], $status);
    }
}