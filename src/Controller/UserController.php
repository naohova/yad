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
            error_log('User data: ' . json_encode($data));
            $user = $this->userService->createUser($data);
            return $this->jsonResponse($response, [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'role' => $user->getRole()
            ], 201);
        } catch (Exception $e) {
            error_log('Error creating user: ' . $e->getMessage());
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function login(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            try {
                $result = $this->userService->authenticate($data['name'], $data['password']);
                return $this->jsonResponse($response, $result);
            } catch (Exception $e) {
                return $this->errorResponse($response, $e->getMessage(), 401);
            }
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage(), 400);
        }
    }
} 