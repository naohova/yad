<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\ORM\EntityManager;
use Exception;

class SystemController extends AbstractController
{
    public function __construct(
        protected EntityManager $entityManager
    ) {}

    public function healthCheck(Request $request, Response $response): Response
    {
        try {
            // Проверяем только соединение с базой данных
            $connection = $this->entityManager->getConnection();
            $connection->executeQuery('SELECT 1');
            
            return $this->jsonResponse($response, [
                'status' => 'ok',
                'database' => 'connected',
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (Exception $e) {
            return $this->jsonResponse($response, [
                'status' => 'error',
                'message' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ], 500);
        }
    }
} 