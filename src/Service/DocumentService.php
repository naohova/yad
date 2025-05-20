<?php

namespace App\Service;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use App\Repository\MaterialRepository;
use App\Validator\DocumentValidator;
use Exception;
use Psr\Http\Message\UploadedFileInterface;

class DocumentService
{
    public function __construct(
        private DocumentRepository $documentRepository,
        private MaterialRepository $materialRepository,
        private DocumentValidator $validator
    ) {}

    public function uploadDocument(array $data, UploadedFileInterface $file): array
    {
        $this->validator->validateUpload($data);
        
        // Проверяем существование материала
        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        // Проверяем загруженный файл
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new Exception('File upload failed');
        }

        // Создаем директорию для загрузок, если её нет
        $uploadDir = __DIR__ . '/../../public/uploads';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Генерируем уникальное имя файла
        $fileName = uniqid() . '_' . $file->getClientFilename();
        $filePath = $uploadDir . '/' . $fileName;

        // Сохраняем файл
        $file->moveTo($filePath);

        // Создаем запись о документе
        $document = new Document();
        $document->setMaterialId($material->getId());
        $document->setType($data['type']);
        $document->setFilePath('/uploads/' . $fileName);
        $document->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

        $this->documentRepository->save($document);
        
        return [
            'id' => $document->getId(),
            'material_id' => $document->getMaterialId(),
            'type' => $document->getType(),
            'file_path' => $document->getFilePath(),
            'created_at' => $document->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z')
        ];
    }

    public function getMaterialDocuments(int $materialId): array
    {
        // Проверяем существование материала
        $material = $this->materialRepository->find($materialId);
        if (!$material) {
            throw new Exception('Material not found');
        }
        
        $documents = $this->documentRepository->findByMaterialId($materialId);
        
        return array_map(function($doc) {
            return [
                'id' => $doc->getId(),
                'material_id' => $doc->getMaterialId(),
                'type' => $doc->getType(),
                'file_path' => $doc->getFilePath(),
                'created_at' => $doc->getCreatedAt()->format('Y-m-d\TH:i:s.u\Z')
            ];
        }, $documents);
    }
}