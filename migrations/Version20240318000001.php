<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240318000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create places table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE places (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(50) NOT NULL,
            description TEXT,
            location VARCHAR(255)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE places');
    }
} 