<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240320AddMaterialFields extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds new fields to materials table: part_number, last_route_point_id, created_at, updated_at';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE materials ADD COLUMN IF NOT EXISTS part_number VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE materials ADD COLUMN IF NOT EXISTS last_route_point_id INTEGER NULL');
        $this->addSql('ALTER TABLE materials ADD COLUMN IF NOT EXISTS created_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP');
        $this->addSql('ALTER TABLE materials ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP');
        
        // Обновляем существующие записи
        $this->addSql('UPDATE materials SET created_at = CURRENT_TIMESTAMP AT TIME ZONE \'UTC\', updated_at = CURRENT_TIMESTAMP AT TIME ZONE \'UTC\' WHERE created_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE materials DROP COLUMN IF EXISTS part_number');
        $this->addSql('ALTER TABLE materials DROP COLUMN IF EXISTS last_route_point_id');
        $this->addSql('ALTER TABLE materials DROP COLUMN IF EXISTS created_at');
        $this->addSql('ALTER TABLE materials DROP COLUMN IF EXISTS updated_at');
    }
} 