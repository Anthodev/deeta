<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216182729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pending_registrations (id UUID NOT NULL, role_id UUID DEFAULT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'NOW()\' NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT \'NOW()\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_814F86B8E7927C74 ON pending_registrations (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_814F86B8F85E0677 ON pending_registrations (username)');
        $this->addSql('CREATE INDEX IDX_814F86B8D60322AC ON pending_registrations (role_id)');
        $this->addSql('COMMENT ON COLUMN pending_registrations.id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN pending_registrations.role_id IS \'(DC2Type:ulid)\'');
        $this->addSql('COMMENT ON COLUMN pending_registrations.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE pending_registrations ADD CONSTRAINT FK_814F86B8D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE roles ALTER created_at SET DEFAULT \'NOW()\'');
        $this->addSql('ALTER TABLE roles ALTER updated_at SET DEFAULT \'NOW()\'');
        $this->addSql('ALTER TABLE users ADD role_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE users ALTER created_at SET DEFAULT \'NOW()\'');
        $this->addSql('ALTER TABLE users ALTER updated_at SET DEFAULT \'NOW()\'');
        $this->addSql('COMMENT ON COLUMN users.role_id IS \'(DC2Type:ulid)\'');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9D60322AC FOREIGN KEY (role_id) REFERENCES roles (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1483A5E9D60322AC ON users (role_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE pending_registrations DROP CONSTRAINT FK_814F86B8D60322AC');
        $this->addSql('DROP TABLE pending_registrations');
        $this->addSql('ALTER TABLE roles ALTER created_at SET DEFAULT \'2024-02-14 18:17:02.220255\'');
        $this->addSql('ALTER TABLE roles ALTER updated_at SET DEFAULT \'2024-02-14 18:17:02.220255\'');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E9D60322AC');
        $this->addSql('DROP INDEX IDX_1483A5E9D60322AC');
        $this->addSql('ALTER TABLE users DROP role_id');
        $this->addSql('ALTER TABLE users ALTER created_at SET DEFAULT \'2024-02-14 18:17:02.220255\'');
        $this->addSql('ALTER TABLE users ALTER updated_at SET DEFAULT \'2024-02-14 18:17:02.220255\'');
    }
}
