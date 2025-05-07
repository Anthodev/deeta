<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414163942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add profile image path to users';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD COLUMN profile_image_path VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT email, username, first_name, last_name, password, plain_password, job_title, company, location, enabled, created_at, updated_at, id, role_id FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, plain_password VARCHAR(255) DEFAULT NULL, job_title VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, enabled INTEGER DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, id VARCHAR(36) NOT NULL, role_id VARCHAR DEFAULT NULL, PRIMARY KEY(id), CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO users (email, username, first_name, last_name, password, plain_password, job_title, company, location, enabled, created_at, updated_at, id, role_id) SELECT email, username, first_name, last_name, password, plain_password, job_title, company, location, enabled, created_at, updated_at, id, role_id FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE INDEX users_role_id ON users (role_id)');
        $this->addSql('CREATE UNIQUE INDEX users_email_unique_constraint ON users (email)');
        $this->addSql('CREATE UNIQUE INDEX users_username_unique_constraint ON users (username)');
    }
}
