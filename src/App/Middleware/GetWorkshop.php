<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\WorkshopRepository;
use Slim\Exception\HttpNotFoundException;

class GetWorkshop
{
    public function __construct(private WorkshopRepository $repository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $workshop = $this->repository->getById((int) $id);
    
        if ($workshop === false) {
    
            throw new HttpNotFoundException($request, message: 'workshop not found');
    
        }

        $request = $request->withAttribute('workshop', $workshop);

        return $handler->handle($request);
    }
}