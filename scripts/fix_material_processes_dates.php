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
    // Получаем все записи
    $sql = "SELECT * FROM material_processes";
    $stmt = $connection->executeQuery($sql);
    $rows = $stmt->fetchAllAssociative();

    foreach ($rows as $row) {
        $updates = [];
        
        // Проверяем и конвертируем каждое поле с датой
        $dateFields = ['planned_start', 'planned_end', 'fact_start', 'fact_end'];
        foreach ($dateFields as $field) {
            if (isset($row[$field]) && $row[$field] !== null) {
                // Если значение похоже на Unix timestamp
                if (is_numeric($row[$field]) && strlen($row[$field]) >= 10) {
                    $sql = "UPDATE material_processes SET $field = to_timestamp(:timestamp) WHERE id = :id";
                    $connection->executeStatement($sql, [
                        'timestamp' => (int)$row[$field],
                        'id' => $row['id']
                    ]);
                    echo "Обновлена дата {$field} для записи с ID {$row['id']}\n";
                }
            }
        }
    }

    echo "Обновление завершено успешно\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
} 