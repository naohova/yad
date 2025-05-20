<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240321FixPlannedRoutesColumns extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix column names in planned_routes table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE planned_routes RENAME COLUMN createdat TO created_at');
        $this->addSql('ALTER TABLE planned_routes RENAME COLUMN updatedat TO updated_at');
        $this->addSql('ALTER TABLE planned_routes RENAME COLUMN deletedat TO deleted_at');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE planned_routes RENAME COLUMN created_at TO createdat');
        $this->addSql('ALTER TABLE planned_routes RENAME COLUMN updated_at TO updatedat');
        $this->addSql('ALTER TABLE planned_routes RENAME COLUMN deleted_at TO deletedat');
    }
} 