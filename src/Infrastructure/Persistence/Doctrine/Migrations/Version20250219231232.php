<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219231232 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE roles (label VARCHAR(255) NOT NULL, code VARCHAR(32) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX roles_label ON roles (label)');
        $this->addSql('CREATE INDEX roles_code ON roles (code)');
        $this->addSql('CREATE UNIQUE INDEX roles_label_unique_constraint ON roles (label)');
        $this->addSql('CREATE UNIQUE INDEX roles_code_unique_constraint ON roles (code)');
        $this->addSql('CREATE TABLE users (email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, plain_password VARCHAR(255) DEFAULT NULL, enabled INTEGER DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, id VARCHAR(36) NOT NULL, role_id VARCHAR DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX users_role_id ON users (role_id)');
        $this->addSql('CREATE UNIQUE INDEX users_email_unique_constraint ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX users_username_unique_constraint ON users (username)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users');
    }
}
