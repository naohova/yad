<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Doctrine\ORM\EntityManager;
use Controller\MaterialController;
use Controller\MovementController;
use Controller\DocumentController;
use Controller\UserController;
use Controller\RouteController;
use Controller\SystemController;
use Controller\TestController;

define('APP_ROOT', dirname(__DIR__));
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require APP_ROOT . '/vendor/autoload.php';

// Загрузка переменных окружения
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Создание и настройка контейнера
$builder = new ContainerBuilder();
$builder->enableCompilation(__DIR__ . '/../var/cache');
$builder->writeProxiesToFile(true, __DIR__ . '/../var/cache/proxies');

// Добавление определений
$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')
                    ->build();

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
        $group->get('/{id:[0-9]+}', [MaterialController::class, 'get']);
        $group->put('/{id:[0-9]+}', [MaterialController::class, 'update']);
        $group->delete('/{id:[0-9]+}', [MaterialController::class, 'delete']);
    });

    // Movement routes
    $group->post('/movements/scan', [MovementController::class, 'scan']);
    $group->get('/movements/material/{materialId}', [MovementController::class, 'history']);

    // Document routes
    $group->post('/documents', [DocumentController::class, 'upload']);
    $group->get('/documents/material/{materialId}', [DocumentController::class, 'list']);

    // User routes
    $group->post('/users', [UserController::class, 'create']);
    $group->post('/login', [UserController::class, 'login']);

    // Route routes
    $group->post('/route-points', [RouteController::class, 'createPoint']);
    $group->post('/routes', [RouteController::class, 'planRoute']);
    $group->get('/routes/material/{materialId}', [RouteController::class, 'getMaterialRoute']);
});

$app->run();