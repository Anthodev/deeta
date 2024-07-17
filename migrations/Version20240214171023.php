<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214171023 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add roles to role table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO roles (id, code, label) VALUES (\'018DA899-FC87-12F2-1A72-2582DAC26B34\', \'ROLE_ADMIN\', \'Admin\')');
        $this->addSql('INSERT INTO roles (id, code, label) VALUES (\'018DA89A-F313-C69E-B0E0-AA5F73F2BA3F\', \'ROLE_USER\', \'User\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM roles WHERE code = \'ROLE_ADMIN\'');
        $this->addSql('DELETE FROM roles WHERE code = \'ROLE_USER\'');
    }
}
