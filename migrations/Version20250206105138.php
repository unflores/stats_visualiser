<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20250206105138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'creation of the theme table';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql('CREATE TEMPORARY TABLE __temp__theme AS SELECT id, parent_id FROM theme');
        $this->addSql('DROP TABLE theme');
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL, code VARCHAR(255) NOT NULL, is_section BOOLEAN NOT NULL, external_id VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO theme (id, parent_id) SELECT id, parent_id FROM __temp__theme');
        $this->addSql('DROP TABLE __temp__theme');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E70877153098 ON theme (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7089F75D7B0 ON theme (external_id)');
    }

    public function down(Schema $schema): void
    {
        
        $this->addSql('CREATE TEMPORARY TABLE __temp__theme AS SELECT id, parent_id FROM theme');
        $this->addSql('DROP TABLE theme');
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL)');
        $this->addSql('INSERT INTO theme (id, parent_id) SELECT id, parent_id FROM __temp__theme');
        $this->addSql('DROP TABLE __temp__theme');
    }
}
