<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class TransportLogRepository
{
    public function __construct(private Database $database)
    {
    }

    public function getAll(): array
    {
        $pdo = $this->database->getConnection();

        $stmt = $pdo->query('SELECT * FROM transport_log');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM transport_log
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string
    {
        $sql = 'INSERT INTO transport_log (product_id, carrier_id, workshop_id)
                VALUES (:product_id, :carrier_id, :workshop_id)';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':product_id', $data['product_id'], PDO::PARAM_INT);

        $stmt->bindValue(':carrier_id', $data['carrier_id'], PDO::PARAM_INT);

        $stmt->bindValue(':workshop_id', $data['workshop_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $pdo->lastInsertId();
    }
    
    public function getByProductIdAndCarrierIdAndWorkshopId(int $productId, int $carrierId, int $workshopId): array|bool
    {
        $sql = 'SELECT *
                FROM transport_log
                WHERE product_id = :product_id
                AND carrier_id = :carrier_id
                AND workshop_id = :workshop_id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);

        $stmt->bindValue(':carrier_id', $carrierId, PDO::PARAM_INT);

        $stmt->bindValue(':workshop_id', $workshopId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}