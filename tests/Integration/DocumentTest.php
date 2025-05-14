<?php

namespace Tests\Integration;

use Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;
use Entity\RfidTag;
use Entity\RoutePoint;
use Entity\Material;

class DocumentTest extends TestCase
{
    private ServerRequestFactory $requestFactory;
    private StreamFactory $streamFactory;
    private ?int $materialId = null;
    private string $rfidTagUid;
    private int $rfidTagId;
    private int $routePointId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestFactory = new ServerRequestFactory();
        $this->streamFactory = new StreamFactory();
        
        // Получаем ID из тестовых данных
        $this->materialId = $this->testData->getMaterial()->getId();
        
        // Создаем тестовую точку маршрута
        $routePoint = new RoutePoint();
        $routePoint->setName('Test Point');
        $routePoint->setType('checkpoint');
        $this->em->persist($routePoint);
        $this->em->flush();
        
        $this->routePointId = $routePoint->getId();

        // Создаем тестовый материал
        $material = new Material();
        $material->setName('Test Material');
        $material->setAmount(100);
        $material->setType('raw_material');
        $this->em->persist($material);
        $this->em->flush();
        
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

    public function testUploadDocument(): int
    {
        // Создаем тестовый файл
        $tempFile = tempnam(sys_get_temp_dir(), 'test_doc');
        if ($tempFile === false) {
            $this->fail('Не удалось создать временный файл');
        }
        
        if (file_put_contents($tempFile, 'Test document content') === false) {
            unlink($tempFile);
            $this->fail('Не удалось записать содержимое во временный файл');
        }

        $uploadedFile = new \Slim\Psr7\UploadedFile(
            $tempFile,
            'test_document.txt',
            'text/plain',
            filesize($tempFile),
            UPLOAD_ERR_OK
        );

        $data = [
            'material_id' => $this->materialId,
            'type' => 'specification'
        ];

        $request = $this->createRequest('POST', '/api/documents', $data)
            ->withUploadedFiles(['document' => $uploadedFile]);
            
        $response = $this->app->handle($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($this->materialId, $responseData['material_id']);
        $this->assertEquals('specification', $responseData['type']);
        $this->assertArrayHasKey('file_path', $responseData);
        $this->assertArrayHasKey('created_at', $responseData);
        
        // Проверяем формат datetime
        $createdAt = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $responseData['created_at']);
        $this->assertInstanceOf(\DateTime::class, $createdAt, 'Неверный формат created_at');
        
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
        
        return $responseData['id'];
    }

    public function testListDocuments(): void
    {
        // Загружаем несколько документов
        $documents = [
            ['type' => 'specification', 'filename' => 'spec.txt'],
            ['type' => 'certificate', 'filename' => 'cert.txt']
        ];

        foreach ($documents as $doc) {
            $tempFile = tempnam(sys_get_temp_dir(), 'test_doc');
            if ($tempFile === false) {
                $this->fail('Не удалось создать временный файл');
            }
            
            if (file_put_contents($tempFile, 'Test document content') === false) {
                unlink($tempFile);
                $this->fail('Не удалось записать содержимое во временный файл');
            }

            $uploadedFile = new \Slim\Psr7\UploadedFile(
                $tempFile,
                $doc['filename'],
                'text/plain',
                filesize($tempFile),
                UPLOAD_ERR_OK
            );

            $data = [
                'material_id' => $this->materialId,
                'type' => $doc['type']
            ];

            $request = $this->createRequest('POST', '/api/documents', $data)
                ->withUploadedFiles(['document' => $uploadedFile]);
                
            $response = $this->app->handle($request);
            $this->assertEquals(201, $response->getStatusCode(), 'Ошибка при загрузке документа');

            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }

        // Получаем список документов материала
        $request = $this->createRequest('GET', "/api/documents/material/{$this->materialId}");
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertIsArray($responseData);
        $this->assertGreaterThanOrEqual(2, count($responseData));
        
        // Проверяем структуру данных документов и формат datetime
        foreach ($responseData as $doc) {
            $this->assertArrayHasKey('id', $doc);
            $this->assertArrayHasKey('material_id', $doc);
            $this->assertArrayHasKey('type', $doc);
            $this->assertArrayHasKey('file_path', $doc);
            $this->assertArrayHasKey('created_at', $doc);
            
            // Проверяем формат datetime
            $createdAt = \DateTime::createFromFormat('Y-m-d\TH:i:s.u\Z', $doc['created_at']);
            $this->assertInstanceOf(\DateTime::class, $createdAt, 'Неверный формат created_at в списке документов');
        }
    }

    protected function tearDown(): void
    {
        // Очищаем тестовые данные только если EntityManager доступен
        if ($this->em !== null) {
            try {
                // Получаем все документы для удаления файлов
                $documents = $this->em->createQuery('SELECT d FROM Entity\Document d')->getResult();
                foreach ($documents as $document) {
                    if (file_exists($document->getFilePath())) {
                        unlink($document->getFilePath());
                    }
                }
                
                $this->em->createQuery('DELETE FROM Entity\Document')->execute();
                $this->em->createQuery('DELETE FROM Entity\Material')->execute();
                $this->em->createQuery('DELETE FROM Entity\MaterialStatus')->execute();
                $this->em->createQuery('DELETE FROM Entity\RfidTag')->execute();
                $this->em->createQuery('DELETE FROM Entity\RoutePoint')->execute();
            } catch (\Exception $e) {
                // Логируем ошибку, но продолжаем выполнение
                error_log("Ошибка при очистке данных в DocumentTest: " . $e->getMessage());
            }
        }
        parent::tearDown();
    }
} 