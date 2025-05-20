<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240319CreateRouteTables extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Создание таблицы route_points
        $table = $schema->createTable('route_points');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('description', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('createdAt', 'datetime');
        $table->addColumn('updatedAt', 'datetime', ['notnull' => false]);
        $table->addColumn('deletedAt', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);

        // Создание таблицы routes
        $table = $schema->createTable('routes');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('material_id', 'integer');
        $table->addColumn('route_point_id', 'integer');
        $table->addColumn('plannedStartDate', 'datetime', ['notnull' => false]);
        $table->addColumn('plannedEndDate', 'datetime', ['notnull' => false]);
        $table->addColumn('actualDate', 'datetime', ['notnull' => false]);
        $table->addColumn('delayReason', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('createdAt', 'datetime');
        $table->addColumn('updatedAt', 'datetime', ['notnull' => false]);
        $table->addColumn('deletedAt', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint('materials', ['material_id'], ['id']);
        $table->addForeignKeyConstraint('route_points', ['route_point_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('routes');
        $schema->dropTable('route_points');
    }
} 