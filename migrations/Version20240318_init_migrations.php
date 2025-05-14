<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240318InitMigrations extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создает таблицу для отслеживания миграций';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('doctrine_migration_versions');
        $table->addColumn('version', 'string', ['length' => 191]);
        $table->addColumn('executed_at', 'datetime', ['notnull' => false]);
        $table->addColumn('execution_time', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['version']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('doctrine_migration_versions');
    }
} 