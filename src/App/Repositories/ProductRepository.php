<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class ProductRepository
{
    public function __construct(private Database $database)
    {
    }

    public function getAll(): array
    {
        $pdo = $this->database->getConnection();

        $stmt = $pdo->query('SELECT * FROM product');
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT *
                FROM product
                WHERE id = :id OR uid = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data): string
    {
        $sql = 'INSERT INTO product (name, description, amount, uid)
                VALUES (:name, :description, :amount, :uid)';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);

        if (empty($data['description'])) {

            $stmt->bindValue(':description', null, PDO::PARAM_NULL);

        } else {

            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);

        }

        $stmt->bindValue(':amount', $data['amount'], PDO::PARAM_INT);
        
        $stmt->bindValue(':uid', $data['uid'], PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId();
    }

    public function update(int $id, array $data): int
    {
        $sql = 'UPDATE product
                SET name = :name,
                    description = :description,
                    amount = :amount,
                    uid = :uid,
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);

        if (empty($data['description'])) {

            $stmt->bindValue(':description', null, PDO::PARAM_NULL);

        } else {

            $stmt->bindValue(':description', $data['description'], PDO::PARAM_STR);

        }

        $stmt->bindValue(':amount', $data['amount'], PDO::PARAM_INT);

        $stmt->bindValue(':uid', $data['uid'], PDO::PARAM_STR);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(string $id): int
    {
        $sql = 'DELETE FROM product
                WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }
}