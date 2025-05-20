<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240321CreateRoutesTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create routes table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE routes (
            id SERIAL PRIMARY KEY,
            material_id INTEGER NOT NULL,
            route_point_id INTEGER NOT NULL,
            planned_start_date TIMESTAMP NULL,
            planned_end_date TIMESTAMP NULL,
            actual_start_date TIMESTAMP NULL,
            actual_end_date TIMESTAMP NULL,
            delay_reason VARCHAR(255) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL,
            deleted_at TIMESTAMP NULL,
            FOREIGN KEY (material_id) REFERENCES materials(id),
            FOREIGN KEY (route_point_id) REFERENCES route_points(id)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE routes');
    }
} 