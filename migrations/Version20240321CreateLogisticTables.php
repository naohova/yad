<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240321CreateLogisticTables extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // Создаем таблицу places
        $places = $schema->createTable('places');
        $places->addColumn('id', 'integer', ['autoincrement' => true]);
        $places->addColumn('name', 'string', ['length' => 255]);
        $places->addColumn('description', 'string', ['length' => 255]);
        $places->addColumn('place_type', 'string', ['length' => 2]);
        $places->addColumn('external_id', 'string', ['length' => 255]);
        $places->setPrimaryKey(['id']);
        $places->addUniqueIndex(['external_id']);

        // Создаем таблицу employees
        $employees = $schema->createTable('employees');
        $employees->addColumn('id', 'integer', ['autoincrement' => true]);
        $employees->addColumn('employee_id', 'string', ['length' => 255]);
        $employees->addColumn('name', 'string', ['length' => 255]);
        $employees->addColumn('second_name', 'string', ['length' => 255]);
        $employees->setPrimaryKey(['id']);
        $employees->addUniqueIndex(['employee_id']);

        // Создаем таблицу processes
        $processes = $schema->createTable('processes');
        $processes->addColumn('id', 'integer', ['autoincrement' => true]);
        $processes->addColumn('process_id', 'integer');
        $processes->addColumn('description', 'string', ['length' => 255]);
        $processes->addColumn('place_id', 'integer');
        $processes->addColumn('responsible_id', 'integer');
        $processes->setPrimaryKey(['id']);
        $processes->addUniqueIndex(['process_id']);
        $processes->addForeignKeyConstraint('places', ['place_id'], ['id']);
        $processes->addForeignKeyConstraint('employees', ['responsible_id'], ['id']);

        // Создаем таблицу material_processes
        $materialProcesses = $schema->createTable('material_processes');
        $materialProcesses->addColumn('id', 'integer', ['autoincrement' => true]);
        $materialProcesses->addColumn('material_id', 'integer');
        $materialProcesses->addColumn('process_id', 'integer');
        $materialProcesses->addColumn('planned_start', 'integer');
        $materialProcesses->addColumn('planned_end', 'integer');
        $materialProcesses->addColumn('fact_start', 'integer', ['notnull' => false]);
        $materialProcesses->addColumn('fact_end', 'integer', ['notnull' => false]);
        $materialProcesses->addColumn('delay_reason', 'string', ['length' => 255, 'notnull' => false]);
        $materialProcesses->setPrimaryKey(['id']);
        $materialProcesses->addForeignKeyConstraint('materials', ['material_id'], ['id']);
        $materialProcesses->addForeignKeyConstraint('processes', ['process_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('material_processes');
        $schema->dropTable('processes');
        $schema->dropTable('employees');
        $schema->dropTable('places');
    }
} 