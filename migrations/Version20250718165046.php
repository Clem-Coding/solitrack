<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250718165046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cash_movements (id INT AUTO_INCREMENT NOT NULL, made_by_id INT NOT NULL, cash_register_session_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount NUMERIC(10, 2) NOT NULL, comment LONGTEXT DEFAULT NULL, type VARCHAR(10) NOT NULL, INDEX IDX_DD9448DD90B9D269 (made_by_id), INDEX IDX_DD9448DD6C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD90B9D269 FOREIGN KEY (made_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD6C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE withdrawals DROP FOREIGN KEY FK_1DD5572F6C172579');
        $this->addSql('ALTER TABLE withdrawals DROP FOREIGN KEY FK_1DD5572F90B9D269');
        $this->addSql('DROP TABLE withdrawals');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE withdrawals (id INT AUTO_INCREMENT NOT NULL, made_by_id INT NOT NULL, cash_register_session_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount NUMERIC(10, 2) NOT NULL, comment LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_1DD5572F90B9D269 (made_by_id), INDEX IDX_1DD5572F6C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE withdrawals ADD CONSTRAINT FK_1DD5572F6C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE withdrawals ADD CONSTRAINT FK_1DD5572F90B9D269 FOREIGN KEY (made_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD90B9D269');
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD6C172579');
        $this->addSql('DROP TABLE cash_movements');
    }
}
