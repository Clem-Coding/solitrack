<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250628102013 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cash_register_closure (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cash_register_session_id INT NOT NULL, closed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closing_cash_amount DOUBLE PRECISION NOT NULL, discrepancy NUMERIC(10, 2) NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_5017171FA76ED395 (user_id), INDEX IDX_5017171F6C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cash_register_sessions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, opening_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', cash_float DOUBLE PRECISION NOT NULL, cash_withdrawal DOUBLE PRECISION NOT NULL, withdrawal_comment LONGTEXT DEFAULT NULL, INDEX IDX_E6A4623EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cash_register_closure ADD CONSTRAINT FK_5017171FA76ED395A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cash_register_closure ADD CONSTRAINT FK_5017171F6C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE cash_register_sessions ADD CONSTRAINT FK_E6A4623EA76ED395A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_register_closure DROP FOREIGN KEY FK_5017171FA76ED395A76ED395');
        $this->addSql('ALTER TABLE cash_register_closure DROP FOREIGN KEY FK_5017171F6C172579');
        $this->addSql('ALTER TABLE cash_register_sessions DROP FOREIGN KEY FK_E6A4623EA76ED395A76ED395');
        $this->addSql('DROP TABLE cash_register_closure');
        $this->addSql('DROP TABLE cash_register_sessions');
    }
}
