<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251023115843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payments (id INT AUTO_INCREMENT NOT NULL, sale_id INT DEFAULT NULL, method VARCHAR(255) NOT NULL, amount NUMERIC(10, 2) NOT NULL, INDEX IDX_65D29B324A7E4868 (sale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE payments ADD CONSTRAINT FK_65D29B324A7E4868 FOREIGN KEY (sale_id) REFERENCES sales (id)');
        $this->addSql('ALTER TABLE sales DROP cash_amount, DROP card_amount');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payments DROP FOREIGN KEY FK_65D29B324A7E4868');
        $this->addSql('DROP TABLE payments');
        $this->addSql('ALTER TABLE sales ADD cash_amount NUMERIC(10, 2) DEFAULT NULL, ADD card_amount NUMERIC(10, 2) DEFAULT NULL');
    }
}
