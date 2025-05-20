<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240320CreateMovementEvents extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('movement_events');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('material_id', 'integer');
        $table->addColumn('route_point_id', 'integer');
        $table->addColumn('scanned_by', 'integer');
        $table->addColumn('scanned_at', 'datetime');
        $table->addColumn('is_deviation', 'boolean');
        $table->addColumn('note', 'string', ['length' => 255]);
        $table->setPrimaryKey(['id']);
        
        // Добавляем внешние ключи
        $table->addForeignKeyConstraint('materials', ['material_id'], ['id']);
        $table->addForeignKeyConstraint('route_points', ['route_point_id'], ['id']);
        $table->addForeignKeyConstraint('users', ['scanned_by'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('movement_events');
    }
} 