<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;

$config = ORMSetup::createAttributeMetadataConfiguration(
    [__DIR__ . '/../src/Entity'],
    true
);

$connection = DriverManager::getConnection([
    'driver' => 'pdo_pgsql',
    'host' => '127.0.0.1',
    'port' => 5432,
    'dbname' => 'postgres',
    'user' => 'postgres',
    'password' => 'root'
], $config);

try {
    // Проверяем существующие колонки
    $columns = $connection->createSchemaManager()->listTableColumns('material_processes');
    $columnNames = array_map(fn($column) => $column->getName(), $columns);

    // Переименовываем колонки если они существуют в старом формате
    if (in_array('plannedstart', $columnNames)) {
        $connection->executeStatement('ALTER TABLE material_processes RENAME COLUMN plannedstart TO planned_start');
    }
    if (in_array('plannedend', $columnNames)) {
        $connection->executeStatement('ALTER TABLE material_processes RENAME COLUMN plannedend TO planned_end');
    }
    if (in_array('factstart', $columnNames)) {
        $connection->executeStatement('ALTER TABLE material_processes RENAME COLUMN factstart TO fact_start');
    }
    if (in_array('factend', $columnNames)) {
        $connection->executeStatement('ALTER TABLE material_processes RENAME COLUMN factend TO fact_end');
    }

    echo "Колонки успешно обновлены\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
} 