<?php

namespace App\Service;

use App\Entity\Place;
use Doctrine\ORM\EntityRepository;
use App\Validator\PlaceValidator;
use Exception;

class PlaceService
{
    public function __construct(
        private EntityRepository $placeRepository,
        private PlaceValidator $validator
    ) {}

    public function createPlace(array $data): Place
    {
        $this->validator->validateCreate($data);

        $place = new Place();
        $place->setName($data['name']);
        $place->setType($data['type']);
        $place->setDescription($data['description'] ?? null);
        $place->setLocation($data['location'] ?? null);

        $this->placeRepository->save($place);
        return $place;
    }

    public function getPlace(int $id): Place
    {
        $place = $this->placeRepository->find($id);
        if (!$place) {
            throw new Exception('Place not found');
        }
        return $place;
    }

    public function getAllPlaces(): array
    {
        return $this->placeRepository->findAll();
    }

    public function updatePlace(int $id, array $data): Place
    {
        $this->validator->validateUpdate($data);

        $place = $this->placeRepository->find($id);
        if (!$place) {
            throw new Exception('Place not found');
        }

        if (isset($data['name'])) {
            $place->setName($data['name']);
        }
        if (isset($data['type'])) {
            $place->setType($data['type']);
        }
        if (isset($data['description'])) {
            $place->setDescription($data['description']);
        }
        if (isset($data['location'])) {
            $place->setLocation($data['location']);
        }

        $this->placeRepository->save($place);
        return $place;
    }

    public function deletePlace(int $id): void
    {
        $place = $this->placeRepository->find($id);
        if (!$place) {
            throw new Exception('Place not found');
        }

        $this->placeRepository->remove($place);
    }
} 