<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use App\Repository\MaterialRepository;
use App\Repository\UserRepository;
use App\Repository\RfidTagRepository;
use App\Repository\DocumentRepository;
use App\Repository\MaterialStatusRepository;
use App\Repository\MaterialReceiptRepository;
use App\Repository\MovementEventRepository;
use App\Repository\RoutePointRepository;
use App\Repository\PlannedRouteRepository;
use App\Repository\RouteRepository;
use App\Repository\EmployeeRepository;
use App\Repository\ProcessRepository;
use App\Repository\PlaceRepository;
use App\Repository\MaterialProcessRepository;
use App\Service\MaterialService;
use App\Service\MovementService;
use App\Service\DocumentService;
use App\Service\UserService;
use App\Service\PlannedRouteService;
use App\Service\RfidTagService;
use App\Service\MaterialReceiptService;
use App\Service\RoutePointService;
use App\Service\MaterialStatusService;
use App\Service\EmployeeService;
use App\Service\ProcessService;
use App\Service\PlaceService;
use App\Service\MaterialProcessService;
use App\Controller\MaterialController;
use App\Controller\MovementController;
use App\Controller\DocumentController;
use App\Controller\UserController;
use App\Controller\RouteController;
use App\Controller\MaterialLogisticController;
use App\Controller\EmployeeController;
use App\Controller\ProcessController;
use App\Controller\PlaceController;
use App\Controller\MaterialProcessController;
use App\Validator\MaterialValidator;
use App\Validator\UserValidator;
use App\Validator\MovementValidator;
use App\Validator\DocumentValidator;
use App\Validator\RouteValidator;
use App\Validator\EmployeeValidator;
use App\Validator\ProcessValidator;
use App\Validator\PlaceValidator;
use App\Validator\MaterialProcessValidator;
use App\Controller\SystemController;
use App\Controller\TestController;
use App\Controller\AbstractController;
use App\Service\RouteService;
use App\Entity\Material;
use App\Entity\User;
use App\Entity\RfidTag;
use App\Entity\Document;
use App\Entity\MaterialStatus;
use App\Entity\MaterialReceipt;
use App\Entity\MovementEvent;
use App\Entity\RoutePoint;
use App\Entity\PlannedRoute;
use App\Entity\Route;
use App\Entity\Employee;
use App\Entity\Process;
use App\Entity\Place;
use App\Entity\MaterialProcess;


return [
    EntityManager::class => function() {
        $config = ORMSetup::createAttributeMetadataConfiguration(
            [__DIR__ . '/../src/Entity'],
            true
        );

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_pgsql',
            'host' => 'localhost',
            'port' => 5432,
            'dbname' => 'postgres',
            'user' => 'postgres',
            'password' => 'root'
        ], $config);

        return new EntityManager($connection, $config);
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

    RouteRepository::class => function(EntityManager $entityManager) {
        return new RouteRepository($entityManager);
    },

    EmployeeRepository::class => function(EntityManager $entityManager) {
        return new EmployeeRepository($entityManager);
    },

    ProcessRepository::class => function(EntityManager $entityManager) {
        return new ProcessRepository($entityManager);
    },

    PlaceRepository::class => function(EntityManager $entityManager) {
        return new PlaceRepository($entityManager);
    },

    MaterialProcessRepository::class => function(EntityManager $entityManager) {
        return new MaterialProcessRepository($entityManager);
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
        MaterialStatusRepository $materialStatusRepository,
        RouteValidator $validator
    ) {
        return new PlannedRouteService(
            $plannedRouteRepository,
            $materialRepository,
            $routePointRepository,
            $materialStatusRepository,
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

    EmployeeService::class => function(EntityManager $entityManager, EmployeeValidator $validator) {
        return new EmployeeService(
            $entityManager->getRepository(Employee::class),
            $validator
        );
    },

    ProcessService::class => function(EntityManager $entityManager, ProcessValidator $validator) {
        return new ProcessService(
            $entityManager->getRepository(Process::class),
            $validator
        );
    },

    PlaceService::class => function(EntityManager $entityManager, PlaceValidator $validator) {
        return new PlaceService(
            $entityManager->getRepository(Place::class),
            $validator
        );
    },

    MaterialProcessService::class => function(EntityManager $entityManager, MaterialProcessValidator $validator) {
        return new MaterialProcessService(
            $entityManager->getRepository(MaterialProcess::class),
            $entityManager->getRepository(Material::class),
            $entityManager->getRepository(Process::class),
            $entityManager->getRepository(Employee::class),
            $entityManager->getRepository(Place::class),
            $validator
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

    RouteController::class => function(RouteService $routeService) {
        return new RouteController($routeService);
    },

    MaterialLogisticController::class => function(EntityManager $entityManager) {
        return new MaterialLogisticController($entityManager);
    },

    EmployeeController::class => function(EntityManager $entityManager, EmployeeService $employeeService) {
        return new EmployeeController($entityManager, $employeeService);
    },

    ProcessController::class => function(EntityManager $entityManager, ProcessService $processService) {
        return new ProcessController($entityManager, $processService);
    },

    PlaceController::class => function(EntityManager $entityManager, PlaceService $placeService) {
        return new PlaceController($entityManager, $placeService);
    },

    MaterialProcessController::class => function(EntityManager $entityManager, MaterialProcessService $materialProcessService) {
        return new MaterialProcessController($entityManager, $materialProcessService);
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

    RouteValidator::class => function(EntityManager $entityManager) {
        return new RouteValidator();
    },

    EmployeeValidator::class => function() {
        return new EmployeeValidator();
    },

    ProcessValidator::class => function() {
        return new ProcessValidator();
    },

    PlaceValidator::class => function() {
        return new PlaceValidator();
    },

    MaterialProcessValidator::class => function() {
        return new MaterialProcessValidator();
    },

    SystemController::class => function(EntityManager $entityManager) {
        return new SystemController($entityManager);
    },

    TestController::class => function(EntityManager $entityManager) {
        return new TestController($entityManager);
    },

    RouteService::class => function(EntityManager $entityManager) {
        return new RouteService(
            new RouteRepository($entityManager),
            new PlannedRouteRepository($entityManager),
            new MaterialRepository($entityManager),
            new RoutePointRepository($entityManager),
            new RouteValidator()
        );
    },
];