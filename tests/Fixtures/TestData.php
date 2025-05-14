<?php

namespace Tests\Fixtures;

use Doctrine\ORM\EntityManager;
use Entity\Material;
use Entity\RfidTag;
use Entity\RoutePoint;
use Entity\User;
use Entity\MaterialStatus;

class TestData
{
    private EntityManager $em;
    private ?User $user = null;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function cleanup(): void
    {
        // Очистка всех таблиц
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();

        try {
            // Отключаем проверку foreign keys на время очистки
            $connection->executeStatement('SET session_replication_role = replica');

            // Получаем все таблицы
            $tables = $connection->createSchemaManager()->listTableNames();

            // Очищаем каждую таблицу
            foreach ($tables as $table) {
                $connection->executeStatement('TRUNCATE TABLE ' . $table . ' CASCADE');
            }

            // Включаем проверку foreign keys обратно
            $connection->executeStatement('SET session_replication_role = DEFAULT');
        } catch (\Exception $e) {
            // В случае ошибки, убеждаемся что foreign keys включены обратно
            $connection->executeStatement('SET session_replication_role = DEFAULT');
            throw $e;
        }
    }

    public function loadBasicData(): void
    {
        // Создаем тестовую точку маршрута
        $routePoint = new RoutePoint();
        $routePoint->setName('Test Point');
        $routePoint->setType('checkpoint');
        $this->em->persist($routePoint);

        // Создаем тестового пользователя
        $this->user = new User();
        $this->user->setName('Test User');
        $this->user->setRole('operator');
        $this->user->setPasswordHash(password_hash('test123', PASSWORD_DEFAULT));
        $this->em->persist($this->user);

        $this->em->flush();
    }

    public function getUser(): User
    {
        if ($this->user === null) {
            $this->user = new User();
            $this->user->setName('Test User');
            $this->user->setRole('operator');
            $this->user->setPasswordHash(password_hash('test123', PASSWORD_DEFAULT));
            $this->em->persist($this->user);
            $this->em->flush();
        }
        return $this->user;
    }

    public function getMaterial(array $data = []): Material
    {
        // Создаем тестовую точку маршрута, если не передана
        if (!isset($data['initial_point_id'])) {
            $routePoint = new RoutePoint();
            $routePoint->setName('Test Point');
            $routePoint->setType('checkpoint');
            $this->em->persist($routePoint);
            $this->em->flush();
            $data['initial_point_id'] = $routePoint->getId();
        }

        // Создаем материал с дефолтными или переданными данными
        $material = new Material();
        $material->setName($data['name'] ?? 'Test Material');
        $material->setAmount($data['amount'] ?? 100);
        $material->setType($data['type'] ?? 'raw_material');
        
        $this->em->persist($material);
        $this->em->flush();

        // Создаем статус материала
        $status = new MaterialStatus();
        $status->setMaterial($material);
        $status->setCurrentPointId($data['initial_point_id']);
        $status->setStatus('created');
        $status->setUpdatedAt(new \DateTime());
        
        // Устанавливаем двунаправленную связь
        $material->setStatus($status);
        
        $this->em->persist($status);
        $this->em->flush();

        // Если передан RFID-тег, создаем его
        if (isset($data['rfid_tag'])) {
            $rfidTag = new RfidTag();
            $rfidTag->setTagUid($data['rfid_tag']);
            $rfidTag->setIsActive(true);
            $rfidTag->setMaterialId($material->getId());
            $rfidTag->setAssignedAt(new \DateTime());
            $this->em->persist($rfidTag);
            $this->em->flush();
        }

        return $material;
    }

    public function getRoutePoint(): RoutePoint
    {
        $routePoint = new RoutePoint();
        $routePoint->setName('Test Point');
        $routePoint->setType('checkpoint');
        $this->em->persist($routePoint);
        $this->em->flush();
        return $routePoint;
    }
} 