<?php

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240321AddPartNumber extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $table = $schema->getTable('materials');
        if (!$table->hasColumn('part_number')) {
            $table->addColumn('part_number', 'string', [
                'length' => 255,
                'notnull' => false
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('materials');
        if ($table->hasColumn('part_number')) {
            $table->dropColumn('part_number');
        }
    }
} 