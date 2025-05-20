<?php

namespace App\Service;

use App\Entity\Place;
use Doctrine\ORM\EntityManager;
use App\Validator\PlaceValidator;
use Exception;

class PlaceService
{
    public function __construct(
        private EntityManager $entityManager,
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

        $this->entityManager->persist($place);
        $this->entityManager->flush();
        return $place;
    }

    public function getPlace(int $id): Place
    {
        $place = $this->entityManager->getRepository(Place::class)->find($id);
        if (!$place) {
            throw new Exception('Place not found');
        }
        return $place;
    }

    public function getAllPlaces(): array
    {
        return $this->entityManager->getRepository(Place::class)->findAll();
    }

    public function updatePlace(int $id, array $data): Place
    {
        $this->validator->validateUpdate($data);

        $place = $this->entityManager->getRepository(Place::class)->find($id);
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

        $this->entityManager->flush();
        return $place;
    }

    public function deletePlace(int $id): void
    {
        $place = $this->entityManager->getRepository(Place::class)->find($id);
        if (!$place) {
            throw new Exception('Place not found');
        }

        $this->entityManager->remove($place);
        $this->entityManager->flush();
    }
} 