<?php

namespace Tests\Integration;

use Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Entity\RfidTag;
use Entity\RoutePoint;
use Entity\User;
use Entity\Material;

class MovementTest extends TestCase
{
    private ServerRequestFactory $requestFactory;
    private StreamFactory $streamFactory;
    private ?int $materialId = null;
    private string $rfidTagUid;
    private int $rfidTagId;
    private int $routePointId;
    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestFactory = new ServerRequestFactory();
        $this->streamFactory = new StreamFactory();
        
        // Получаем ID из тестовых данных
        $this->materialId = $this->testData->getMaterial()->getId();
        $this->routePointId = $this->testData->getRoutePoint()->getId();
        $this->userId = $this->testData->getUser()->getId();

        // Создаем тестовую точку маршрута
        $routePoint = new RoutePoint();
        $routePoint->setName('Test Point');
        $routePoint->setType('checkpoint');
        $this->em->persist($routePoint);
        $this->em->flush();
        
        $this->routePointId = $routePoint->getId();

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

    public function testScanMaterial(): void
    {
        $data = [
            'material_id' => $this->materialId,
            'route_point_id' => $this->routePointId,
            'scanned_by' => $this->userId,
            'note' => 'Test scan'
        ];

        $request = $this->createRequest('POST', '/api/movements/scan', $data);
        $response = $this->app->handle($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($data['material_id'], $responseData['material_id']);
        $this->assertEquals($data['route_point_id'], $responseData['route_point_id']);
        $this->assertEquals($data['scanned_by'], $responseData['scanned_by']);
        $this->assertEquals($data['note'], $responseData['note']);
        $this->assertArrayHasKey('scanned_at', $responseData);
        $this->assertArrayHasKey('is_deviation', $responseData);
    }

    public function testGetMovementHistory(): void
    {
        // Создаем несколько сканирований
        $scanPoints = [
            ['route_point_id' => $this->routePointId, 'note' => 'First scan'],
            ['route_point_id' => $this->routePointId, 'note' => 'Second scan']
        ];

        foreach ($scanPoints as $point) {
            $data = [
                'material_id' => $this->materialId,
                'route_point_id' => $point['route_point_id'],
                'scanned_by' => $this->userId,
                'note' => $point['note']
            ];

            $request = $this->createRequest('POST', '/api/movements/scan', $data);
            $response = $this->app->handle($request);
            $this->assertEquals(201, $response->getStatusCode(), 'Ошибка при создании сканирования');
        }

        // Получаем историю движений
        $request = $this->createRequest('GET', "/api/movements/material/{$this->materialId}");
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('events', $responseData);
        $this->assertIsArray($responseData['events']);
        $this->assertGreaterThanOrEqual(2, count($responseData['events']));
        
        foreach ($responseData['events'] as $event) {
            $this->assertIsArray($event);
            $this->assertArrayHasKey('id', $event);
            $this->assertArrayHasKey('material_id', $event);
            $this->assertArrayHasKey('route_point_id', $event);
            $this->assertArrayHasKey('scanned_by', $event);
            $this->assertArrayHasKey('scanned_at', $event);
            $this->assertArrayHasKey('is_deviation', $event);
            $this->assertArrayHasKey('note', $event);
        }
    }

    protected function tearDown(): void
    {
        // Очищаем тестовые данные только если EntityManager доступен
        if ($this->em !== null) {
            try {
                $this->em->createQuery('DELETE FROM Entity\MovementEvent')->execute();
                $this->em->createQuery('DELETE FROM Entity\Material')->execute();
                $this->em->createQuery('DELETE FROM Entity\MaterialStatus')->execute();
                $this->em->createQuery('DELETE FROM Entity\RfidTag')->execute();
                $this->em->createQuery('DELETE FROM Entity\RoutePoint')->execute();
                $this->em->createQuery('DELETE FROM Entity\User')->execute();
            } catch (\Exception $e) {
                // Игнорируем ошибки при очистке
            }
        }
        parent::tearDown();
    }
} 