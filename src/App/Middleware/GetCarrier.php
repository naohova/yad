<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use App\Repositories\CarrierRepository;
use Slim\Exception\HttpNotFoundException;

class GetCarrier
{
    public function __construct(private CarrierRepository $repository)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($request);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $carrier = $this->repository->getById((int) $id);
    
        if ($carrier === false) {
    
            throw new HttpNotFoundException($request, message: 'carrier not found');
    
        }

        $request = $request->withAttribute('carrier', $carrier);

        return $handler->handle($request);
    }
}