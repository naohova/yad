<?php

namespace Repository;

use Doctrine\ORM\EntityManager;
use Entity\Material;

class MaterialRepository extends AbstractRepository
{
    public function __construct(EntityManager $entityManager)
    {
        parent::__construct($entityManager, Material::class);
    }

    public function findByType(string $type): array
    {
        return $this->repository->findBy(['type' => $type]);
    }

    public function findByName(string $name): array
    {
        return $this->repository->findBy(['name' => $name]);
    }

    public function findByParams(array $params): array
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('m')
           ->from('Entity\Material', 'm');

        $conditions = [];
        $parameters = [];

        foreach ($params as $field => $value) {
            switch ($field) {
                case 'name':
                    $conditions[] = 'm.name LIKE :name';
                    $parameters['name'] = '%' . $value . '%';
                    break;
                case 'type':
                    $conditions[] = 'm.type = :type';
                    $parameters['type'] = $value;
                    break;
                case 'amount':
                    if (is_array($value)) {
                        if (isset($value['min'])) {
                            $conditions[] = 'm.amount >= :amountMin';
                            $parameters['amountMin'] = $value['min'];
                        }
                        if (isset($value['max'])) {
                            $conditions[] = 'm.amount <= :amountMax';
                            $parameters['amountMax'] = $value['max'];
                        }
                    } else {
                        $conditions[] = 'm.amount = :amount';
                        $parameters['amount'] = $value;
                    }
                    break;
                case 'parent_id':
                    $conditions[] = 'm.parentId = :parentId';
                    $parameters['parentId'] = $value;
                    break;
                case 'deleted':
                    if ($value) {
                        $conditions[] = 'm.deletedAt IS NOT NULL';
                    } else {
                        $conditions[] = 'm.deletedAt IS NULL';
                    }
                    break;
                case 'part_number':
                    $conditions[] = 'm.partNumber LIKE :partNumber';
                    $parameters['partNumber'] = '%' . $value . '%';
                    break;
                case 'last_route_point_id':
                    $conditions[] = 'm.lastRoutePointId = :lastRoutePointId';
                    $parameters['lastRoutePointId'] = $value;
                    break;
                case 'created_at':
                    if (is_array($value)) {
                        if (isset($value['from'])) {
                            $conditions[] = 'm.createdAt >= :createdAtFrom';
                            $parameters['createdAtFrom'] = new \DateTime($value['from']);
                        }
                        if (isset($value['to'])) {
                            $conditions[] = 'm.createdAt <= :createdAtTo';
                            $parameters['createdAtTo'] = new \DateTime($value['to']);
                        }
                    } else {
                        $conditions[] = 'DATE(m.createdAt) = :createdAt';
                        $parameters['createdAt'] = new \DateTime($value);
                    }
                    break;
                case 'updated_at':
                    if (is_array($value)) {
                        if (isset($value['from'])) {
                            $conditions[] = 'm.updatedAt >= :updatedAtFrom';
                            $parameters['updatedAtFrom'] = new \DateTime($value['from']);
                        }
                        if (isset($value['to'])) {
                            $conditions[] = 'm.updatedAt <= :updatedAtTo';
                            $parameters['updatedAtTo'] = new \DateTime($value['to']);
                        }
                    } else {
                        $conditions[] = 'DATE(m.updatedAt) = :updatedAt';
                        $parameters['updatedAt'] = new \DateTime($value);
                    }
                    break;
            }
        }

        if (!empty($conditions)) {
            $qb->where(implode(' AND ', $conditions));
            foreach ($parameters as $key => $value) {
                $qb->setParameter($key, $value);
            }
        }

        return $qb->getQuery()->getResult();
    }
}