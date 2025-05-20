<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240322RemovePartNumberFromMaterialProcesses extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Removes part_number column from material_processes table as it should come from material entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE material_processes DROP COLUMN IF EXISTS part_number');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE material_processes ADD COLUMN part_number VARCHAR(255) NULL');
    }
} 