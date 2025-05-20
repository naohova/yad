<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240322FixMaterialProcessesDateTypes extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Changes date column types in material_processes table';
    }

    public function up(Schema $schema): void
    {
        // Сначала конвертируем существующие значения в timestamp
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN planned_start TYPE timestamp USING to_timestamp(planned_start)');
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN planned_end TYPE timestamp USING to_timestamp(planned_end)');
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN fact_start TYPE timestamp USING to_timestamp(fact_start)');
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN fact_end TYPE timestamp USING to_timestamp(fact_end)');
    }

    public function down(Schema $schema): void
    {
        // В случае отката конвертируем обратно в bigint
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN planned_start TYPE bigint USING extract(epoch from planned_start)');
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN planned_end TYPE bigint USING extract(epoch from planned_end)');
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN fact_start TYPE bigint USING extract(epoch from fact_start)');
        $this->addSql('ALTER TABLE material_processes ALTER COLUMN fact_end TYPE bigint USING extract(epoch from fact_end)');
    }
} 