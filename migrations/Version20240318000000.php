<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240318000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update employees table structure';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE employees DROP COLUMN employee_id');
        $this->addSql('ALTER TABLE employees DROP COLUMN second_name');
        $this->addSql('ALTER TABLE employees ADD COLUMN position VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE employees ADD COLUMN department VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE employees ADD COLUMN email VARCHAR(255)');
        $this->addSql('ALTER TABLE employees ADD COLUMN phone VARCHAR(20)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE employees ADD COLUMN employee_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE employees ADD COLUMN second_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE employees DROP COLUMN position');
        $this->addSql('ALTER TABLE employees DROP COLUMN department');
        $this->addSql('ALTER TABLE employees DROP COLUMN email');
        $this->addSql('ALTER TABLE employees DROP COLUMN phone');
    }
} 