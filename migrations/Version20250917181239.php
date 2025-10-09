<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917181239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE volunteer_sessions CHANGE start_datetime start_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE end_datetime end_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE volunteer_sessions CHANGE start_datetime start_datetime DATETIME NOT NULL, CHANGE end_datetime end_datetime DATETIME NOT NULL, CHANGE created_at created_at DATETIME NOT NULL');
    }
}
