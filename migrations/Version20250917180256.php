<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917180256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE volunteer_recurrences (id INT AUTO_INCREMENT NOT NULL, frequency VARCHAR(255) NOT NULL, until_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE volunteer_registrations (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, user_id INT NOT NULL, status VARCHAR(20) NOT NULL, registered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CD9BA599613FECDF (session_id), INDEX IDX_CD9BA599A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE volunteer_sessions (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, recurrence_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, start_datetime DATETIME NOT NULL, end_datetime DATETIME NOT NULL, required_volunteers INT DEFAULT NULL, is_cancelled TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B2BCCD92B03A8386 (created_by_id), INDEX IDX_B2BCCD922C414CE8 (recurrence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE volunteer_registrations ADD CONSTRAINT FK_CD9BA599613FECDF FOREIGN KEY (session_id) REFERENCES volunteer_sessions (id)');
        $this->addSql('ALTER TABLE volunteer_registrations ADD CONSTRAINT FK_CD9BA599A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE volunteer_sessions ADD CONSTRAINT FK_B2BCCD92B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE volunteer_sessions ADD CONSTRAINT FK_B2BCCD922C414CE8 FOREIGN KEY (recurrence_id) REFERENCES volunteer_recurrences (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE volunteer_registrations DROP FOREIGN KEY FK_CD9BA599613FECDF');
        $this->addSql('ALTER TABLE volunteer_registrations DROP FOREIGN KEY FK_CD9BA599A76ED395');
        $this->addSql('ALTER TABLE volunteer_sessions DROP FOREIGN KEY FK_B2BCCD92B03A8386');
        $this->addSql('ALTER TABLE volunteer_sessions DROP FOREIGN KEY FK_B2BCCD922C414CE8');
        $this->addSql('DROP TABLE volunteer_recurrences');
        $this->addSql('DROP TABLE volunteer_registrations');
        $this->addSql('DROP TABLE volunteer_sessions');
    }
}
