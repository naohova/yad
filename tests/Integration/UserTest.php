<?php

namespace Tests\Integration;

use Tests\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;

class UserTest extends TestCase
{
    private ServerRequestFactory $requestFactory;
    private StreamFactory $streamFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestFactory = new ServerRequestFactory();
        $this->streamFactory = new StreamFactory();
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

    public function testCreateUser(): int
    {
        $data = [
            'name' => 'test_user',
            'password' => 'test_password',
            'role' => 'operator'
        ];

        $request = $this->createRequest('POST', '/api/users', $data);
        $response = $this->app->handle($request);
        
        $this->assertEquals(201, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($data['name'], $responseData['name']);
        $this->assertEquals($data['role'], $responseData['role']);
        $this->assertArrayNotHasKey('password', $responseData);
        $this->assertArrayNotHasKey('password_hash', $responseData);
        
        return $responseData['id'];
    }

    public function testUserLogin(): void
    {
        // Сначала создаем пользователя
        $userData = [
            'name' => 'login_test_user',
            'password' => 'test_password',
            'role' => 'operator'
        ];

        $request = $this->createRequest('POST', '/api/users', $userData);
        $this->app->handle($request);

        // Пытаемся залогиниться
        $loginData = [
            'name' => $userData['name'],
            'password' => $userData['password']
        ];

        $request = $this->createRequest('POST', '/api/auth/login', $loginData);
        $response = $this->app->handle($request);
        
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode((string)$response->getBody(), true);
        $this->assertArrayHasKey('token', $responseData);
        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals($userData['name'], $responseData['user']['name']);
        $this->assertEquals($userData['role'], $responseData['user']['role']);
    }

    public function testInvalidLogin(): void
    {
        $loginData = [
            'name' => 'nonexistent_user',
            'password' => 'wrong_password'
        ];

        $request = $this->createRequest('POST', '/api/auth/login', $loginData);
        $response = $this->app->handle($request);
        
        $this->assertEquals(401, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        // Очищаем тестовые данные
        $this->em->createQuery('DELETE FROM Entity\User')->execute();
        parent::tearDown();
    }
} 