<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220331093401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
       // $this->addSql('ALTER TABLE participant DROP photo, CHANGE administrateur administrateur TINYINT(1) DEFAULT 0 NOT NULL');
        //$this->addSql('CREATE UNIQUE INDEX UNIQ_D79F6B1186CC499D ON participant (pseudo)');
      //  $this->addSql('ALTER TABLE sortie RENAME INDEX idx_3c3fd3f29d1c3019 TO IDX_3C3FD3F2D936B2FA');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP INDEX UNIQ_D79F6B1186CC499D ON participant');
        $this->addSql('ALTER TABLE participant ADD photo BLOB DEFAULT NULL, CHANGE administrateur administrateur TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE sortie RENAME INDEX idx_3c3fd3f2d936b2fa TO IDX_3C3FD3F29D1C3019');
    }
}
