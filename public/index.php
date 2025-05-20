<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Doctrine\ORM\EntityManager;
use App\Controller\MaterialController;
use App\Controller\MovementController;
use App\Controller\DocumentController;
use App\Controller\UserController;
use App\Controller\RouteController;
use App\Controller\SystemController;
use App\Controller\TestController;
use App\Controller\MaterialLogisticController;
use App\Controller\EmployeeController;
use App\Controller\ProcessController;
use App\Controller\PlaceController;
use App\Controller\MaterialProcessController;
use DI\Container;

define('APP_ROOT', dirname(__DIR__));
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require APP_ROOT . '/vendor/autoload.php';

// Загрузка переменных окружения
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Create Container
$containerBuilder = new ContainerBuilder();

// Add definitions
$definitions = require APP_ROOT . '/config/definitions.php';
$containerBuilder->addDefinitions($definitions);

// Build Container
$container = $containerBuilder->build();

// Create App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Настройка маршрутизации
$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs());

// Middleware
$app->addBodyParsingMiddleware();
$app->addErrorMiddleware(true, true, true);

$app->group('/api', function ($group) {
    // Health check
    $group->get('/health', [SystemController::class, 'healthCheck']);
    
    // Test route
    $group->get('/test', [TestController::class, 'hello']);
    
    // Material routes
    $group->group('/materials', function ($group) {
        $group->get('', [MaterialController::class, 'list']);
        $group->post('', [MaterialController::class, 'create']);
        $group->get('/{id}', [MaterialController::class, 'get']);
        $group->put('/{id}', [MaterialController::class, 'update']);
        $group->delete('/{id}', [MaterialController::class, 'delete']);
        $group->post('/{id}/assemble', [MaterialController::class, 'assemble']);
        $group->get('/{id}/logistic', [MaterialLogisticController::class, 'getMaterialLogistic']);
    });

    // Movement routes
    $group->post('/movements/scan', [MovementController::class, 'scan']);
    $group->get('/movements/material/{materialId}', [MovementController::class, 'history']);

    // Document routes
    $group->post('/documents', [DocumentController::class, 'upload']);
    $group->get('/documents/material/{materialId}', [DocumentController::class, 'list']);

    // User routes
    $group->post('/users', [UserController::class, 'create']);
    $group->post('/auth/login', [UserController::class, 'login']);

    // Route Points
    $group->post('/route-points', [RouteController::class, 'createPoint']);
    $group->get('/route-points', [RouteController::class, 'list']);
    $group->get('/route-points/{id}', [RouteController::class, 'get']);
    $group->put('/route-points/{id}', [RouteController::class, 'update']);
    $group->delete('/route-points/{id}', [RouteController::class, 'delete']);

    // Routes
    $group->post('/routes/planned', [RouteController::class, 'createPlannedRoute']);
    $group->post('/routes/actual', [RouteController::class, 'createActualRoute']);
    $group->get('/routes', [RouteController::class, 'list']);
    $group->get('/routes/{id}', [RouteController::class, 'get']);
    $group->put('/routes/{id}', [RouteController::class, 'update']);
    $group->delete('/routes/{id}', [RouteController::class, 'delete']);

    // Employee routes
    $group->group('/employees', function ($group) {
        $group->get('', [EmployeeController::class, 'list']);
        $group->post('', [EmployeeController::class, 'create']);
        $group->get('/{id}', [EmployeeController::class, 'get']);
        $group->put('/{id}', [EmployeeController::class, 'update']);
        $group->delete('/{id}', [EmployeeController::class, 'delete']);
    });

    // Process routes
    $group->group('/processes', function ($group) {
        $group->get('', [ProcessController::class, 'list']);
        $group->post('', [ProcessController::class, 'create']);
        $group->get('/{id}', [ProcessController::class, 'get']);
        $group->put('/{id}', [ProcessController::class, 'update']);
        $group->delete('/{id}', [ProcessController::class, 'delete']);
    });

    // Place routes
    $group->group('/places', function ($group) {
        $group->get('', [PlaceController::class, 'list']);
        $group->post('', [PlaceController::class, 'create']);
        $group->get('/{id}', [PlaceController::class, 'get']);
        $group->put('/{id}', [PlaceController::class, 'update']);
        $group->delete('/{id}', [PlaceController::class, 'delete']);
    });

    // Material Process routes
    $group->group('/material-processes', function ($group) {
        $group->get('', [MaterialProcessController::class, 'list']);
        $group->post('', [MaterialProcessController::class, 'create']);
        $group->get('/{id}', [MaterialProcessController::class, 'get']);
        $group->put('/{id}', [MaterialProcessController::class, 'update']);
        $group->delete('/{id}', [MaterialProcessController::class, 'delete']);
        $group->get('/material/{material_id}', [MaterialProcessController::class, 'getByMaterial']);
        $group->get('/employee/{employee_id}', [MaterialProcessController::class, 'getByEmployee']);
        $group->get('/place/{place_id}', [MaterialProcessController::class, 'getByPlace']);
    });
});

// Возвращаем приложение если это тестовое окружение, иначе запускаем
if (defined('PHPUNIT_RUNNING')) {
    return $app;
} else {
    $app->run();
}