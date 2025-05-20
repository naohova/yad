<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240322UpdateMaterialProcessColumns extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Updates column names in material_processes table to use snake_case';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN plannedstart TO planned_start');
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN plannedend TO planned_end');
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN factstart TO fact_start');
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN factend TO fact_end');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN planned_start TO plannedstart');
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN planned_end TO plannedend');
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN fact_start TO factstart');
        $this->addSql('ALTER TABLE material_processes RENAME COLUMN fact_end TO factend');
    }
} 