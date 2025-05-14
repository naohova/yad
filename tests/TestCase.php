<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Doctrine\ORM\EntityManager;
use Tests\Fixtures\TestData;
use DI\Container;
use Slim\App;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;

abstract class TestCase extends BaseTestCase
{
    protected ?EntityManager $em = null;
    protected Container $container;
    protected TestData $testData;
    protected App $app;

    protected function setUp(): void
    {
        parent::setUp();

        // Определяем корневую директорию приложения
        if (!defined('APP_ROOT')) {
            define('APP_ROOT', dirname(__DIR__));
        }
        
        // Загружаем переменные окружения для тестов
        if (file_exists(APP_ROOT . '/.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable(APP_ROOT, '.env');
            $dotenv->load();
        }

        // Создаем контейнер
        $builder = new ContainerBuilder();
        $this->container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')
                                 ->build();

        // Создаем приложение
        AppFactory::setContainer($this->container);
        $this->app = AppFactory::create();

        // Настраиваем middleware
        $this->app->addBodyParsingMiddleware();
        $this->app->addErrorMiddleware(true, true, true);

        // Настройка маршрутизации
        $collector = $this->app->getRouteCollector();
        $collector->setDefaultInvocationStrategy(new RequestResponseArgs());

        // Подключаем все маршруты из index.php
        $this->app->group('/api', function ($group) {
            // Health check
            $group->get('/health', [\Controller\SystemController::class, 'healthCheck']);
            
            // Test route
            $group->get('/test', [\Controller\TestController::class, 'hello']);
            
            // Material routes
            $group->group('/materials', function ($group) {
                $group->get('', [\Controller\MaterialController::class, 'list']);
                $group->post('', [\Controller\MaterialController::class, 'create']);
                $group->get('/{id:[0-9]+}', [\Controller\MaterialController::class, 'get']);
                $group->put('/{id:[0-9]+}', [\Controller\MaterialController::class, 'update']);
                $group->delete('/{id:[0-9]+}', [\Controller\MaterialController::class, 'delete']);
            });

            // Movement routes
            $group->post('/movements/scan', [\Controller\MovementController::class, 'scan']);
            $group->get('/movements/material/{materialId}', [\Controller\MovementController::class, 'history']);

            // Document routes
            $group->post('/documents', [\Controller\DocumentController::class, 'upload']);
            $group->get('/documents/material/{materialId}', [\Controller\DocumentController::class, 'list']);

            // User routes
            $group->post('/users', [\Controller\UserController::class, 'create']);
            $group->post('/auth/login', [\Controller\UserController::class, 'login']);
            $group->post('/auth/logout', [\Controller\UserController::class, 'logout']);

            // Route routes
            $group->post('/route-points', [\Controller\RouteController::class, 'createPoint']);
            $group->post('/routes', [\Controller\RouteController::class, 'planRoute']);
            $group->get('/routes/material/{materialId}', [\Controller\RouteController::class, 'getMaterialRoute']);
        });
        
        // Получаем EntityManager
        $this->em = $this->container->get(EntityManager::class);
        
        // Инициализация тестовых данных
        $this->testData = new TestData($this->em);
        
        // Очистка базы данных перед каждым тестом
        $this->testData->cleanup();
        
        // Загрузка базовых тестовых данных
        $this->testData->loadBasicData();
    }

    protected function tearDown(): void
    {
        // Очистка данных после каждого теста
        if ($this->testData) {
            $this->testData->cleanup();
        }
        
        parent::tearDown();
    }
}