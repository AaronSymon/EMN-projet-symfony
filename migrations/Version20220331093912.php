<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331093912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images ADD image_profil_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AFFDC0B2C FOREIGN KEY (image_profil_id) REFERENCES participant (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6AFFDC0B2C ON images (image_profil_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D79F6B1186CC499D ON participant (pseudo)');
        $this->addSql('ALTER TABLE sortie RENAME INDEX idx_3c3fd3f29d1c3019 TO IDX_3C3FD3F2D936B2FA');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AFFDC0B2C');
        $this->addSql('DROP INDEX UNIQ_E01FBE6AFFDC0B2C ON images');
        $this->addSql('ALTER TABLE images DROP image_profil_id');
        $this->addSql('DROP INDEX UNIQ_D79F6B1186CC499D ON participant');
        $this->addSql('ALTER TABLE sortie RENAME INDEX idx_3c3fd3f2d936b2fa TO IDX_3C3FD3F29D1C3019');
    }
}
