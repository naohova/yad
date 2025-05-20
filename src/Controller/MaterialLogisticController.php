<?php

namespace App\Controller;

use Entity\Material;
use Entity\MaterialProcess;
use Entity\Process;
use Entity\Place;
use Entity\Employee;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MaterialLogisticController extends AbstractController
{
    private EntityManager $entityManager;

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
                'place_type' => $place->getPlaceType(),
                'process' => [
                    'process_id' => $process->getProcessId(),
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
                        $responsible->getEmployeeId() => [
                            'employee_id' => $responsible->getEmployeeId(),
                            'name' => $responsible->getName(),
                            'second_name' => $responsible->getSecondName()
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