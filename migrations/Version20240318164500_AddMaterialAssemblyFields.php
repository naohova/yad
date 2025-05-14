<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240318164500AddMaterialAssemblyFields extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавляет поля deleted_at и parent_id в таблицу materials для поддержки сборки материалов';
    }

    public function up(Schema $schema): void
    {
        // Добавляем поле deleted_at
        $this->addSql('ALTER TABLE materials ADD COLUMN deleted_at TIMESTAMP DEFAULT NULL');

        // Добавляем поле parent_id и внешний ключ
        $this->addSql('ALTER TABLE materials ADD COLUMN parent_id INTEGER DEFAULT NULL');
        $this->addSql('ALTER TABLE materials ADD CONSTRAINT FK_MATERIAL_PARENT FOREIGN KEY (parent_id) REFERENCES materials (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // Удаляем внешний ключ
        $this->addSql('ALTER TABLE materials DROP CONSTRAINT IF EXISTS FK_MATERIAL_PARENT');
        
        // Удаляем поля
        $this->addSql('ALTER TABLE materials DROP COLUMN IF EXISTS parent_id');
        $this->addSql('ALTER TABLE materials DROP COLUMN IF EXISTS deleted_at');
    }
} 