<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250514205356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE skills ADD COLUMN is_text_white BOOLEAN DEFAULT false NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__skills AS SELECT label, default_color, position, created_at, updated_at, id, user_id FROM skills
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE skills
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE skills (label VARCHAR(255) NOT NULL, default_color VARCHAR(255) DEFAULT NULL, position INTEGER DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, id VARCHAR(36) NOT NULL, user_id VARCHAR(36) DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_D5311670A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO skills (label, default_color, position, created_at, updated_at, id, user_id) SELECT label, default_color, position, created_at, updated_at, id, user_id FROM __temp__skills
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__skills
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX skills_user_id ON skills (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX skills_label_unique_constraint ON skills (label)
        SQL);
    }
}
