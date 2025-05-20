<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240322AddPartNumberToMaterialProcesses extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds part_number column to material_processes table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE material_processes ADD COLUMN part_number VARCHAR(255) NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE material_processes DROP COLUMN IF EXISTS part_number');
    }
} 