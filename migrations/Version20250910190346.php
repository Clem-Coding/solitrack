<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250910190346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD90B9D269');
        $this->addSql('ALTER TABLE cash_movements CHANGE made_by_id made_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD90B9D269 FOREIGN KEY (made_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE visitors DROP FOREIGN KEY FK_7B74A43FA76ED395');
        $this->addSql('ALTER TABLE visitors CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD90B9D269');
        $this->addSql('ALTER TABLE cash_movements CHANGE made_by_id made_by_id INT NOT NULL');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD90B9D269 FOREIGN KEY (made_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE visitors DROP FOREIGN KEY FK_7B74A43FA76ED395');
        $this->addSql('ALTER TABLE visitors CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }
}
