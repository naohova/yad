<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Service\UserService;
use Exception;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService
    ) {}

    public function create(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $user = $this->userService->createUser($data);
            return $this->jsonResponse($response, $user, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function login(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $result = $this->userService->authenticate($data['name'], $data['password']);
            
            if (!$result) {
                return $this->errorResponse($response, 'Invalid credentials', 401);
            }

            return $this->jsonResponse($response, [
                'token' => $result['token'],
                'user' => $result['user']
            ]);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
} 