<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250629100721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE withdrawal (id INT AUTO_INCREMENT NOT NULL, made_by_id INT NOT NULL, cash_register_session_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount NUMERIC(10, 2) NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_6D2D3B4590B9D269 (made_by_id), INDEX IDX_6D2D3B456C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE withdrawal ADD CONSTRAINT FK_6D2D3B4590B9D269 FOREIGN KEY (made_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE withdrawal ADD CONSTRAINT FK_6D2D3B456C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE cash_register_sessions DROP cash_withdrawal, DROP withdrawal_comment');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE withdrawal DROP FOREIGN KEY FK_6D2D3B4590B9D269');
        $this->addSql('ALTER TABLE withdrawal DROP FOREIGN KEY FK_6D2D3B456C172579');
        $this->addSql('DROP TABLE withdrawal');
        $this->addSql('ALTER TABLE cash_register_sessions ADD cash_withdrawal DOUBLE PRECISION DEFAULT NULL, ADD withdrawal_comment LONGTEXT DEFAULT NULL');
    }
}
