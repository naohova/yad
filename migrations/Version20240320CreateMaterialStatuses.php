<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240320CreateMaterialStatuses extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('material_statuses');
        $table->addColumn('material_id', 'integer');
        $table->addColumn('current_point_id', 'integer');
        $table->addColumn('status', 'string', ['length' => 255]);
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['material_id']);
        
        // Добавляем внешние ключи
        $table->addForeignKeyConstraint('materials', ['material_id'], ['id']);
        $table->addForeignKeyConstraint('route_points', ['current_point_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('material_statuses');
    }
} 