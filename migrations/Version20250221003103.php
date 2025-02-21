<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221003103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'modify parent_id_column string by integer';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE theme (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, parent_id INTEGER DEFAULT NULL, code VARCHAR(255) NOT NULL, is_section BOOLEAN NOT NULL, external_id VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E70877153098 ON theme (code)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9775E7089F75D7B0 ON theme (external_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE theme');
    }
}
