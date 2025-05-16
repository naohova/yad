<?php

namespace Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Service\DocumentService;
use Exception;

class DocumentController extends AbstractController
{
    public function __construct(
        private DocumentService $documentService
    ) {}

    public function upload(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $files = $request->getUploadedFiles();
            
            if (!isset($files['document'])) {
                throw new Exception('No document file provided');
            }

            $document = $this->documentService->uploadDocument($data, $files['document']);
            return $this->jsonResponse($response, $document, 201);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }

    public function list(Request $request, Response $response, string $materialId): Response
    {
        try {
            if (!is_numeric($materialId)) {
                throw new Exception('Material ID must be a number');
            }
            $materialId = (int)$materialId;
            $documents = $this->documentService->getMaterialDocuments($materialId);
            return $this->jsonResponse($response, $documents);
        } catch (Exception $e) {
            return $this->errorResponse($response, $e->getMessage());
        }
    }
}