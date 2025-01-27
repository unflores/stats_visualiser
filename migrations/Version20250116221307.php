<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250116221307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Build theme table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE theme');
    }
}
