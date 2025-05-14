<?php

namespace Tests\Integration;

use Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Entity\RfidTag;
use Entity\RoutePoint;
use Entity\Material;

class RouteTest extends TestCase
{
    private ServerRequestFactory $requestFactory;
    private StreamFactory $streamFactory;
    private ?int $materialId = null;
    private array $routePoints = [];
    private string $rfidTagUid;
    private int $rfidTagId;
    private int $routePointId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestFactory = new ServerRequestFactory();
        $this->streamFactory = new StreamFactory();
        
        // Создаем тестовые точки маршрута
        $this->routePoints = [];
        for ($i = 0; $i < 3; $i++) {
            $point = new RoutePoint();
            $point->setName('Test Point ' . ($i + 1));
            $point->setType('checkpoint');
            $this->em->persist($point);
            $this->em->flush();
            
            $this->routePoints[] = [
                'route_point_id' => $point->getId(),
                'name' => $point->getName(),
                'type' => $point->getType()
            ];
        }
        
        // Создаем тестовый материал с явным указанием начальной точки
        $material = $this->testData->getMaterial([
            'name' => 'Test Material',
            'amount' => 100,
            'type' => 'raw_material',
            'initial_point_id' => $this->routePoints[0]['route_point_id']
        ]);
        $this->materialId = $material->getId();
        
        // Создаем тестовую RFID метку
        $this->rfidTagUid = 'TEST-TAG-' . uniqid();
        $rfidTag = new RfidTag();
        $rfidTag->setTagUid($this->rfidTagUid);
        $rfidTag->setIsActive(true);
        $rfidTag->setMaterialId($this->materialId);
        $rfidTag->setAssignedAt(new \DateTime());
        $this->em->persist($rfidTag);
        $this->em->flush();
        
        $this->rfidTagId = $rfidTag->getId();
    }

    protected function createRequest(
        string $method,
        string $path,
        array $body = null
    ): Request {
        $request = $this->requestFactory->createServerRequest($method, $path);
        
        if ($body !== null) {
            $stream = $this->streamFactory->createStream(json_encode($body));
            $request = $request->withBody($stream)
                             ->withHeader('Content-Type', 'application/json');
        }
        
        return $request;
    }

    public function testCreateRoutePoint(): void
    {
        $data = [
            'name' => 'New Route Point',
            'type' => 'checkpoint'
        ];

        $request = $this->createRequest('POST', '/api/route-points', $data);
        $response = $this->app->handle($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertEquals($data['type'], $responseData['type']);
    }

    public function testPlanRoute(): void
    {
        $expectedAt = (new \DateTime())->modify('+1 day')->format('Y-m-d\TH:i:s.u\Z');
        
        $routePoints = [];
        foreach ($this->routePoints as $index => $point) {
            $routePoints[] = [
                'route_point_id' => $point['route_point_id'],
                'sequence' => $index + 1,
                'expected_at' => $expectedAt
            ];
        }

        $data = [
            'material_id' => $this->materialId,
            'route_points' => $routePoints
        ];

        $request = $this->createRequest('POST', '/api/routes', $data);
        $response = $this->app->handle($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($responseData);
        $this->assertCount(count($this->routePoints), $responseData);
        
        foreach ($responseData as $index => $routePoint) {
            $this->assertArrayHasKey('id', $routePoint);
            $this->assertEquals($this->materialId, $routePoint['material_id']);
            $this->assertEquals($this->routePoints[$index]['route_point_id'], $routePoint['route_point_id']);
            $this->assertEquals($index + 1, $routePoint['sequence']);
            $this->assertArrayHasKey('expected_at', $routePoint);
            
            // Проверяем формат datetime
            $expectedAtDate = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $routePoint['expected_at']);
            $this->assertInstanceOf(\DateTime::class, $expectedAtDate, 'Неверный формат expected_at');
        }
        
        // Проверяем обновление MaterialStatus
        $material = $this->em->getRepository('Entity\Material')->find($this->materialId);
        $materialStatus = $material->getStatus();
        $this->assertNotNull($materialStatus, 'MaterialStatus не был обновлен');
        $this->assertEquals($this->routePoints[0]['route_point_id'], $materialStatus->getCurrentPointId());
    }

    public function testGetMaterialRoute(): void
    {
        // Сначала создаем маршрут
        $this->testPlanRoute();

        // Затем получаем его
        $request = $this->createRequest('GET', '/api/routes/material/' . $this->materialId);
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($responseData);
        $this->assertNotEmpty($responseData);
        
        foreach ($responseData as $route) {
            $this->assertArrayHasKey('id', $route);
            $this->assertArrayHasKey('material_id', $route);
            $this->assertArrayHasKey('route_point_id', $route);
            $this->assertArrayHasKey('sequence', $route);
            $this->assertArrayHasKey('expected_at', $route);
            
            // Проверяем формат даты
            $expectedAt = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $route['expected_at']);
            $this->assertInstanceOf(\DateTime::class, $expectedAt, 'Неверный формат expected_at в точке маршрута');
        }
    }

    protected function tearDown(): void
    {
        // Очищаем тестовые данные только если EntityManager доступен
        if ($this->em !== null) {
            try {
                $this->em->createQuery('DELETE FROM Entity\PlannedRoute')->execute();
                $this->em->createQuery('DELETE FROM Entity\Material')->execute();
                $this->em->createQuery('DELETE FROM Entity\MaterialStatus')->execute();
                $this->em->createQuery('DELETE FROM Entity\RfidTag')->execute();
                $this->em->createQuery('DELETE FROM Entity\RoutePoint')->execute();
            } catch (\Exception $e) {
                // Логируем ошибку, но продолжаем выполнение
                error_log("Ошибка при очистке данных в RouteTest: " . $e->getMessage());
            }
        }
        parent::tearDown();
    }
} 