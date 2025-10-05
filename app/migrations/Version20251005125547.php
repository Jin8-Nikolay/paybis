<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005125547 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $table = $schema->createTable('rates');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->setPrimaryKey(['id']);

        $table->addColumn('pair', 'string', ['length' => 20]);
        $table->addColumn('price', 'decimal', ['precision' => 18, 'scale' => 8]);
        $table->addColumn('created_at', 'datetime');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('rates');
    }
}
