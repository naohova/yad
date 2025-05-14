<?php

namespace Tests\Integration;

use Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Entity\RfidTag;
use Entity\RoutePoint;

class MaterialTest extends TestCase
{
    private ServerRequestFactory $requestFactory;
    private StreamFactory $streamFactory;
    private string $rfidTagUid;
    private int $rfidTagId;
    private int $routePointId;
    private ?int $testMaterialId = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestFactory = new ServerRequestFactory();
        $this->streamFactory = new StreamFactory();
        $this->rfidTagUid = 'TEST123';

        // Создаем тестовую точку маршрута
        $routePoint = new RoutePoint();
        $routePoint->setName('Test Point');
        $routePoint->setType('checkpoint');
        $this->em->persist($routePoint);
        $this->em->flush();
        
        $this->routePointId = $routePoint->getId();
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

    public function testCreateMaterial(): void
    {
        // Создаем тестовую точку маршрута
        $routePoint = new RoutePoint();
        $routePoint->setName('Test Point');
        $routePoint->setType('checkpoint');
        $this->em->persist($routePoint);
        $this->em->flush();
        
        $this->routePointId = $routePoint->getId();

        $data = [
            'name' => 'Test Material',
            'amount' => 100,
            'type' => 'raw_material',
            'rfid_tag' => $this->rfidTagUid,
            'initial_point_id' => $this->routePointId
        ];

        $request = $this->createRequest('POST', '/api/materials', $data);
        $response = $this->app->handle($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertEquals($data['amount'], $responseData['amount']);
        $this->assertEquals($data['type'], $responseData['type']);

        // Сохраняем ID для использования в других тестах
        $this->testMaterialId = $responseData['id'];

        // После создания материала создаем RFID метку
        $rfidTag = new RfidTag();
        $rfidTag->setTagUid($this->rfidTagUid);
        $rfidTag->setIsActive(true);
        $rfidTag->setMaterialId($responseData['id']);
        $rfidTag->setAssignedAt(new \DateTime());
        $this->em->persist($rfidTag);
        $this->em->flush();
        
        $this->rfidTagId = $rfidTag->getId();
    }

    public function testGetMaterial(): void
    {
        // Сначала создаем материал
        $this->testCreateMaterial();
        
        $request = $this->createRequest('GET', "/api/materials/{$this->testMaterialId}");
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertEquals($this->testMaterialId, $responseData['id']);
        $this->assertArrayHasKey('status', $responseData, 'Material should include status information');
    }

    public function testUpdateMaterial(): void
    {
        // Сначала создаем материал
        $this->testCreateMaterial();
        
        $data = [
            'name' => 'Updated Material',
            'amount' => 150,
            'type' => 'finished_product'
        ];

        $request = $this->createRequest('PUT', "/api/materials/{$this->testMaterialId}", $data);
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertEquals($this->testMaterialId, $responseData['id']);
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertEquals($data['amount'], $responseData['amount']);
        $this->assertEquals($data['type'], $responseData['type']);
    }

    public function testDeleteMaterial(): void
    {
        // Сначала создаем материал
        $this->testCreateMaterial();
        
        $request = $this->createRequest('DELETE', "/api/materials/{$this->testMaterialId}");
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        // Проверяем, что материал действительно удален
        $request = $this->createRequest('GET', "/api/materials/{$this->testMaterialId}");
        $response = $this->app->handle($request);
        
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testListMaterials(): void
    {
        // Создаем несколько материалов для теста
        $materials = [
            [
                'name' => 'Material 1',
                'amount' => 100,
                'type' => 'raw_material',
                'rfid_tag' => $this->rfidTagUid,
                'initial_point_id' => $this->routePointId
            ],
            [
                'name' => 'Material 2',
                'amount' => 200,
                'type' => 'finished_product',
                'rfid_tag' => $this->rfidTagUid,
                'initial_point_id' => $this->routePointId
            ]
        ];

        foreach ($materials as $material) {
            $request = $this->createRequest('POST', '/api/materials', $material);
            $this->app->handle($request);
        }

        // Получаем список материалов
        $request = $this->createRequest('GET', '/api/materials');
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($responseData);
        $this->assertGreaterThanOrEqual(2, count($responseData));
    }

    protected function tearDown(): void
    {
        // Очищаем тестовые данные только если EntityManager доступен
        if ($this->em !== null) {
            try {
                $this->em->createQuery('DELETE FROM Entity\RfidTag')->execute();
                $this->em->createQuery('DELETE FROM Entity\Material')->execute();
                $this->em->createQuery('DELETE FROM Entity\MaterialStatus')->execute();
            } catch (\Exception $e) {
                // Игнорируем ошибки при очистке
            }
        }
        parent::tearDown();
    }
} 