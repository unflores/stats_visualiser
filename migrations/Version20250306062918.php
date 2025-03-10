<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250306062918 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename column code to name in theme table.';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__theme AS SELECT id, parent_Id, is_section, code, external_id FROM theme');
        $this->addSql('DROP TABLE theme');
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_Id INTEGER DEFAULT NULL, is_section BOOLEAN NOT NULL, name VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO theme (id, parent_Id, is_section, name, external_id) SELECT id, parent_Id, is_section, code, external_id FROM __temp__theme');
        $this->addSql('DROP TABLE __temp__theme');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7089F75D7B0 ON theme (external_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7085E237E06 ON theme (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__theme AS SELECT id, parent_id, name, is_section, external_id FROM theme');
        $this->addSql('DROP TABLE theme');
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL, code VARCHAR(255) NOT NULL, is_section BOOLEAN NOT NULL, external_id VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO theme (id, parent_id, code, is_section, external_id) SELECT id, parent_id, name, is_section, external_id FROM __temp__theme');
        $this->addSql('DROP TABLE __temp__theme');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7089F75D7B0 ON theme (external_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E70877153098 ON theme (code)');
    }
}
