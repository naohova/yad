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
    // Изменяем типы колонок на timestamp
    $queries = [
        'ALTER TABLE material_processes ALTER COLUMN planned_start TYPE timestamp USING to_timestamp(CASE WHEN planned_start IS NULL THEN 0 ELSE planned_start END)',
        'ALTER TABLE material_processes ALTER COLUMN planned_end TYPE timestamp USING to_timestamp(CASE WHEN planned_end IS NULL THEN 0 ELSE planned_end END)',
        'ALTER TABLE material_processes ALTER COLUMN fact_start TYPE timestamp USING to_timestamp(CASE WHEN fact_start IS NULL THEN 0 ELSE fact_start END)',
        'ALTER TABLE material_processes ALTER COLUMN fact_end TYPE timestamp USING to_timestamp(CASE WHEN fact_end IS NULL THEN 0 ELSE fact_end END)'
    ];

    foreach ($queries as $sql) {
        $connection->executeStatement($sql);
        echo "Выполнен запрос: $sql\n";
    }

    echo "Типы колонок успешно изменены\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
} 