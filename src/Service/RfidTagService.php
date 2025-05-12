<?php

namespace Service;

use Entity\RfidTag;
use Repository\RfidTagRepository;
use Repository\MaterialRepository;
use Exception;

class RfidTagService
{
    public function __construct(
        private RfidTagRepository $rfidTagRepository,
        private MaterialRepository $materialRepository
    ) {}

    public function assignTag(array $data): RfidTag
    {
        // Проверяем, не используется ли уже этот тег
        $existingTag = $this->rfidTagRepository->findByTagUid($data['tag_uid']);
        if ($existingTag && $existingTag->isActive()) {
            throw new Exception('RFID tag is already in use');
        }

        // Проверяем существование материала
        $material = $this->materialRepository->find($data['material_id']);
        if (!$material) {
            throw new Exception('Material not found');
        }

        $tag = new RfidTag();
        $tag->setMaterialId($material->getId());
        $tag->setTagUid($data['tag_uid']);
        $tag->setIsActive(true);
        $tag->setAssignedAt(date('Y-m-d H:i:s'));

        $this->rfidTagRepository->save($tag);
        return $tag;
    }

    public function deactivateTag(int $tagId): RfidTag
    {
        $tag = $this->rfidTagRepository->find($tagId);
        if (!$tag) {
            throw new Exception('RFID tag not found');
        }

        $tag->setIsActive(false);
        $this->rfidTagRepository->save($tag);
        return $tag;
    }
}