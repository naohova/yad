<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240322AddTypeToRoutePoints extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add type field to route_points table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE route_points ADD COLUMN type VARCHAR(50) NOT NULL DEFAULT \'POINT\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE route_points DROP COLUMN type');
    }
} 