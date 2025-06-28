<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250628110502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cash_register_closures (id INT AUTO_INCREMENT NOT NULL, closed_by_id INT DEFAULT NULL, cash_register_session_id INT NOT NULL, closed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closing_cash_amount DOUBLE PRECISION NOT NULL, discrepancy NUMERIC(10, 2) NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_9656D5E9E1FA7797 (closed_by_id), INDEX IDX_9656D5E96C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cash_register_closures ADD CONSTRAINT FK_9656D5E9E1FA7797E1FA7797 FOREIGN KEY (closed_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cash_register_closures ADD CONSTRAINT FK_9656D5E96C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE cash_register_closure DROP FOREIGN KEY FK_5017171FE1FA7797E1FA7797');
        $this->addSql('ALTER TABLE cash_register_closure DROP FOREIGN KEY FK_5017171F6C172579');
        $this->addSql('DROP TABLE cash_register_closure');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cash_register_closure (id INT AUTO_INCREMENT NOT NULL, closed_by_id INT DEFAULT NULL, cash_register_session_id INT NOT NULL, closed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closing_cash_amount DOUBLE PRECISION NOT NULL, discrepancy NUMERIC(10, 2) NOT NULL, note LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_5017171F6C172579 (cash_register_session_id), INDEX IDX_5017171FE1FA7797 (closed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE cash_register_closure ADD CONSTRAINT FK_5017171FE1FA7797E1FA7797 FOREIGN KEY (closed_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cash_register_closure ADD CONSTRAINT FK_5017171F6C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE cash_register_closures DROP FOREIGN KEY FK_9656D5E9E1FA7797E1FA7797');
        $this->addSql('ALTER TABLE cash_register_closures DROP FOREIGN KEY FK_9656D5E96C172579');
        $this->addSql('DROP TABLE cash_register_closures');
    }
}
