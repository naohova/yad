<?php

namespace App\Controller;

use App\Entity\Place;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Doctrine\ORM\EntityManager;
use App\Service\PlaceService;

class PlaceController extends AbstractController
{
    private PlaceService $placeService;

    public function __construct(EntityManager $entityManager, PlaceService $placeService)
    {
        parent::__construct($entityManager);
        $this->placeService = $placeService;
    }

    public function list(Request $request, Response $response): Response
    {
        $places = $this->placeService->getAllPlaces();
        return $this->jsonResponse($response, $places);
    }

    public function get(Request $request, Response $response, array $args): Response
    {
        $place = $this->placeService->getPlace((int)$args['id']);
        if (!$place) {
            return $this->notFoundResponse($response);
        }
        return $this->jsonResponse($response, $place);
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $place = $this->placeService->createPlace($data);
        return $this->jsonResponse($response, $place, 201);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $place = $this->placeService->updatePlace((int)$args['id'], $data);
        if (!$place) {
            return $this->notFoundResponse($response);
        }
        return $this->jsonResponse($response, $place);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $result = $this->placeService->deletePlace((int)$args['id']);
        if (!$result) {
            return $this->notFoundResponse($response);
        }
        return $response->withStatus(204);
    }
} 