<?php

namespace App\Controller;

use App\Entity\Material;
use App\Entity\MaterialProcess;
use App\Entity\Process;
use App\Entity\Place;
use App\Entity\Employee;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MaterialLogisticController extends AbstractController
{
    protected EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getMaterialLogistic(Request $request, Response $response, string $id): Response
    {
        $materialId = (int)$id;
        
        $material = $this->entityManager->getRepository(Material::class)->find($materialId);
        if (!$material) {
            return $this->errorResponse($response, 'Material not found', 404);
        }

        $materialProcesses = $this->entityManager->getRepository(MaterialProcess::class)
            ->findBy(['material' => $material], ['plannedStart' => 'ASC']);

        $logisticMap = [];
        foreach ($materialProcesses as $materialProcess) {
            $process = $materialProcess->getProcess();
            $place = $process->getPlace();
            $responsible = $process->getResponsible();

            $logisticMap[$place->getExternalId()] = [
                'name' => $place->getName(),
                'description' => $place->getDescription(),
                'place_id' => $place->getId(),
                'place_type' => $place->getType(),
                'process' => [
                    'process_id' => $process->getId(),
                    'planned_timestamp' => [
                        $materialProcess->getPlannedStart(),
                        $materialProcess->getPlannedEnd()
                    ],
                    'fact_timestamp' => [
                        $materialProcess->getFactStart(),
                        $materialProcess->getFactEnd()
                    ],
                    'description' => $process->getDescription(),
                    'responsible' => [
                        $responsible->getId() => [
                            'employee_id' => $responsible->getId(),
                            'name' => $responsible->getName(),
                            'position' => $responsible->getPosition(),
                            'department' => $responsible->getDepartment()
                        ]
                    ]
                ]
            ];
        }

        $result = [
            'productId' => $material->getId(),
            'detail_id' => $material->getPartNumber(),
            'logistic_map' => $logisticMap
        ];

        return $this->jsonResponse($response, $result);
    }
} 