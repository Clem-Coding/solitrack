<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250910183515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_movements CHANGE id id INT AUTO_INCREMENT NOT NULL, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD90B9D269 FOREIGN KEY (made_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD6C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('CREATE INDEX IDX_DD9448DD90B9D269 ON cash_movements (made_by_id)');
        $this->addSql('CREATE INDEX IDX_DD9448DD6C172579 ON cash_movements (cash_register_session_id)');
        $this->addSql('ALTER TABLE cash_register_sessions DROP cash_withdrawal, DROP withdrawal_comment');
        $this->addSql('ALTER TABLE sales DROP FOREIGN KEY FK_6B817044A76ED395');
        $this->addSql('ALTER TABLE sales CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sales ADD CONSTRAINT FK_6B817044A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales DROP FOREIGN KEY FK_6B817044A76ED395');
        $this->addSql('ALTER TABLE sales CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE sales ADD CONSTRAINT FK_6B817044A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE cash_movements MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD90B9D269');
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD6C172579');
        $this->addSql('DROP INDEX IDX_DD9448DD90B9D269 ON cash_movements');
        $this->addSql('DROP INDEX IDX_DD9448DD6C172579 ON cash_movements');
        $this->addSql('DROP INDEX `primary` ON cash_movements');
        $this->addSql('ALTER TABLE cash_movements CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE cash_register_sessions ADD cash_withdrawal DOUBLE PRECISION DEFAULT NULL, ADD withdrawal_comment LONGTEXT DEFAULT NULL');
    }
}
