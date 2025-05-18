<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\Uid\Ulid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518172926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add admin and user roles';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO roles (id, label, code) VALUES ("'.Ulid::generate().'", "ROLE_ADMIN", "ROLE_ADMIN")');
        $this->addSql('INSERT INTO roles (id, label, code) VALUES ("'.Ulid::generate().'", "ROLE_USER", "ROLE_USER")');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
