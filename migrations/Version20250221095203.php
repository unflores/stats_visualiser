<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250221095203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add columns and indexes into theme table';
    }

    public function up(Schema $schema): void
    {
        
        $this->addSql('ALTER TABLE theme ADD COLUMN is_section BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE theme ADD COLUMN code VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE theme ADD COLUMN external_id VARCHAR(255) NOT NULL');
        
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_9775E70877153098 ON theme (code)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_9775E7089F75D7B0 ON theme (external_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE theme DROP COLUMN parent_id');
        $this->addSql('ALTER TABLE theme DROP COLUMN is_section');
        $this->addSql('ALTER TABLE theme DROP COLUMN code');
        
        $this->addSql('DROP INDEX IF EXISTS UNIQ_9775E70877153098');
        $this->addSql('DROP INDEX IF EXISTS UNIQ_9775E7089F75D7B0');
    }
}

