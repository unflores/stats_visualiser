<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250128223139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('ALTER TABLE theme RENAME COLUMN `parent_id` TO `parentId`');
        $this->addSql('ALTER TABLE theme ADD COLUMN `is_section` BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE theme ADD COLUMN `code` VARCHAR(255) NOT NULL');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE theme');
    }
}
