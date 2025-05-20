<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240321CreatePlannedRoutesTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create planned_routes table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE planned_routes (
            id SERIAL PRIMARY KEY,
            material_id INTEGER NOT NULL,
            route_point_id INTEGER NOT NULL,
            sequence INTEGER NOT NULL,
            expected_at TIMESTAMP NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (material_id) REFERENCES materials(id),
            FOREIGN KEY (route_point_id) REFERENCES route_points(id)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE planned_routes');
    }
} 