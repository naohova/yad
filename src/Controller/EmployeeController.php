<?php

namespace App\Controller;

use App\Entity\Employee;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\ORM\EntityManager;
use App\Service\EmployeeService;

class EmployeeController extends AbstractController
{
    private EmployeeService $employeeService;

    public function __construct(EntityManager $entityManager, EmployeeService $employeeService)
    {
        parent::__construct($entityManager);
        $this->employeeService = $employeeService;
    }

    public function list(Request $request, Response $response): Response
    {
        $employees = $this->employeeService->getAllEmployees();
        return $this->jsonResponse($response, $employees);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $employee = $this->employeeService->getEmployee((int)$args['id']);
        if (!$employee) {
            return $this->notFoundResponse($response);
        }
        return $this->jsonResponse($response, $employee);
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $employee = $this->employeeService->createEmployee($data);
        return $this->jsonResponse($response, $employee, 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $employee = $this->employeeService->updateEmployee((int)$args['id'], $data);
        if (!$employee) {
            return $this->notFoundResponse($response);
        }
        return $this->jsonResponse($response, $employee);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $result = $this->employeeService->deleteEmployee((int)$args['id']);
        if (!$result) {
            return $this->notFoundResponse($response);
        }
        return $response->withStatus(204);
    }
} 