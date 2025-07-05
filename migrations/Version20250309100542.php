<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250309100542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales ADD user_id INT NOT NULL, ADD total_price NUMERIC(10, 2) NOT NULL, ADD cash_amount NUMERIC(10, 2) DEFAULT NULL, ADD card_amount NUMERIC(10, 2) DEFAULT NULL, ADD tip NUMERIC(10, 2) DEFAULT NULL, ADD zipcode_customer VARCHAR(12) DEFAULT NULL');
        $this->addSql('ALTER TABLE sales ADD CONSTRAINT FK_6B817044A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_6B817044A76ED395 ON sales (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales DROP FOREIGN KEY FK_6B817044A76ED395');
        $this->addSql('DROP INDEX IDX_6B817044A76ED395 ON sales');
        $this->addSql('ALTER TABLE sales DROP user_id, DROP total_price, DROP cash_amount, DROP card_amount, DROP tip, DROP zipcode_customer');
    }
}
