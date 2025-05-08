<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\GetTransportLogRepository;
use Slim\Exception\HttpNotFoundException;

class GetProduct
{
    public function __construct(private GetTransportLogRepository $repository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $transport_log = $this->repository->getById((int) $id);
    
        if ($transport_log === false) {
    
            throw new HttpNotFoundException($request, message: 'transport_log not found');
    
        }

        $request = $request->withAttribute('transport_log', $transport_log);

        return $handler->handle($request);
    }
}