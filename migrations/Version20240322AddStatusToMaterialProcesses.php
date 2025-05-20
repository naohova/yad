<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240322AddStatusToMaterialProcesses extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $table = $schema->getTable('material_processes');
        if (!$table->hasColumn('status')) {
            $table->addColumn('status', 'string', [
                'length' => 50,
                'notnull' => true
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('material_processes');
        if ($table->hasColumn('status')) {
            $table->dropColumn('status');
        }
    }
} 