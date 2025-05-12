<?php

use Doctrine\ORM\EntityManager;
use Repository\MaterialRepository;
use Repository\UserRepository;
use Repository\RfidTagRepository;
use Repository\DocumentRepository;
use Repository\MaterialStatusRepository;
use Repository\MaterialReceiptRepository;
use Repository\MovementEventRepository;
use Repository\RoutePointRepository;
use Repository\PlannedRouteRepository;
use Service\MaterialService;
use Service\MovementService;
use Service\DocumentService;
use Service\UserService;
use Service\PlannedRouteService;
use Service\RfidTagService;
use Service\MaterialReceiptService;
use Service\RoutePointService;
use Service\MaterialStatusService;
use Controller\MaterialController;
use Controller\MovementController;
use Controller\DocumentController;
use Controller\UserController;
use Controller\RouteController;
use Validator\MaterialValidator;
use Validator\UserValidator;
use Validator\MovementValidator;
use Validator\DocumentValidator;
use Validator\RouteValidator;
use Controller\SystemController;
use Controller\TestController;
use Controller\AbstractController;


return [
    EntityManager::class => function() {
        return require __DIR__ . '/doctrine.php';
    },

    // Repositories
    MaterialRepository::class => function(EntityManager $entityManager) {
        return new MaterialRepository($entityManager);
    },

    UserRepository::class => function(EntityManager $entityManager) {
        return new UserRepository($entityManager);
    },

    RfidTagRepository::class => function(EntityManager $entityManager) {
        return new RfidTagRepository($entityManager);
    },

    DocumentRepository::class => function(EntityManager $entityManager) {
        return new DocumentRepository($entityManager);
    },

    MaterialStatusRepository::class => function(EntityManager $entityManager) {
        return new MaterialStatusRepository($entityManager);
    },

    MaterialReceiptRepository::class => function(EntityManager $entityManager) {
        return new MaterialReceiptRepository($entityManager);
    },

    MovementEventRepository::class => function(EntityManager $entityManager) {
        return new MovementEventRepository($entityManager);
    },

    RoutePointRepository::class => function(EntityManager $entityManager) {
        return new RoutePointRepository($entityManager);
    },

    PlannedRouteRepository::class => function(EntityManager $entityManager) {
        return new PlannedRouteRepository($entityManager);
    },
    // Services
    MaterialService::class => function(
        MaterialRepository $materialRepository,
        RfidTagRepository $rfidTagRepository,
        MaterialStatusRepository $materialStatusRepository,
        MaterialValidator $validator
    ) {
        return new MaterialService($materialRepository, $rfidTagRepository, $materialStatusRepository, $validator);
    },

    MovementService::class => function(
        MovementEventRepository $movementEventRepository,
        MaterialStatusRepository $materialStatusRepository,
        MaterialRepository $materialRepository,
        RoutePointRepository $routePointRepository,
        PlannedRouteRepository $plannedRouteRepository,
        MovementValidator $validator
    ) {
        return new MovementService(
            $movementEventRepository,
            $materialStatusRepository,
            $materialRepository,
            $routePointRepository,
            $plannedRouteRepository,
            $validator
        );
    },

    DocumentService::class => function(
        DocumentRepository $documentRepository,
        MaterialRepository $materialRepository,
        DocumentValidator $validator
    ) {
        return new DocumentService($documentRepository, $materialRepository, $validator);
    },

    UserService::class => function(UserRepository $userRepository, UserValidator $validator) {
        return new UserService($userRepository, $validator);
    },

    PlannedRouteService::class => function(
        PlannedRouteRepository $plannedRouteRepository,
        MaterialRepository $materialRepository,
        RoutePointRepository $routePointRepository,
        RouteValidator $validator
    ) {
        return new PlannedRouteService(
            $plannedRouteRepository,
            $materialRepository,
            $routePointRepository,
            $validator
        );
    },
    RfidTagService::class => function(
        RfidTagRepository $rfidTagRepository,
        MaterialRepository $materialRepository
    ) {
        return new RfidTagService($rfidTagRepository, $materialRepository);
    },

    MaterialReceiptService::class => function(
        MaterialReceiptRepository $materialReceiptRepository,
        MaterialRepository $materialRepository,
        UserRepository $userRepository
    ) {
        return new MaterialReceiptService(
            $materialReceiptRepository,
            $materialRepository,
            $userRepository
        );
    },

    RoutePointService::class => function(
        RoutePointRepository $routePointRepository,
        RouteValidator $validator
    ) {
        return new RoutePointService($routePointRepository, $validator);
    },

    MaterialStatusService::class => function(
        MaterialStatusRepository $materialStatusRepository,
        MaterialRepository $materialRepository,
        RoutePointRepository $routePointRepository
    ) {
        return new MaterialStatusService(
            $materialStatusRepository,
            $materialRepository,
            $routePointRepository
        );
    },
     // Controllers
     MaterialController::class => function(MaterialService $materialService) {
        return new MaterialController($materialService);
    },

    MovementController::class => function(MovementService $movementService) {
        return new MovementController($movementService);
    },

    DocumentController::class => function(DocumentService $documentService) {
        return new DocumentController($documentService);
    },

    UserController::class => function(UserService $userService) {
        return new UserController($userService);
    },

    RouteController::class => function(
        PlannedRouteService $plannedRouteService,
        RoutePointService $routePointService
    ) {
        return new RouteController($plannedRouteService, $routePointService);
    },
        // Validators
        MaterialValidator::class => function() {
            return new MaterialValidator();
        },
        UserValidator::class => function() {
            return new UserValidator();
        },
        MovementValidator::class => function() {
            return new MovementValidator();
        },
        DocumentValidator::class => function() {
            return new DocumentValidator();
        },
        RouteValidator::class => function() {
            return new RouteValidator();
        },
    SystemController::class => function(EntityManager $entityManager) {
        return new SystemController($entityManager);
    },
    TestController::class => function() {
        return new TestController();
    },
];