<?php

namespace Service;

use Entity\Document;
use Repository\DocumentRepository;
use Repository\MaterialRepository;
use Validator\DocumentValidator;
use Exception;

class DocumentService
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private MaterialRepository $materialRepository,
        private DocumentValidator $validator
    ) {}

    public function uploadDocument(array $data, $file): Document
    {
        $this->validator->validateUpload($data);
        // Проверяем существование материала
        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        // Проверяем загруженный файл
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed');
        }

        // Генерируем уникальное имя файла
        $fileName = uniqid() . '_' . $file['name'];
        $filePath = __DIR__ . '/../../public/uploads/' . $fileName;

        // Сохраняем файл
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception('Failed to save file');
        }

        // Создаем запись о документе
        $document = new Document();
        $document->setMaterialId($material->getId());
        $document->setType($data['type']);
        $document->setFilePath('/uploads/' . $fileName);
        $document->setCreatedAt(date('Y-m-d H:i:s'));

        $this->documentRepository->save($document);
        return $document;
    }

    public function getMaterialDocuments(int $materialId): array
    {
        return $this->documentRepository->findByMaterialId($materialId);
    }
}