<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TestController extends AbstractController
{
    public function hello(Request $request, Response $response): Response
    {
        return $this->jsonResponse($response, [
            'message' => 'Hello World!',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
} 