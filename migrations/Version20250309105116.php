<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250309105116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales_items ADD sale_id INT NOT NULL, ADD weigth NUMERIC(10, 2) DEFAULT NULL, ADD price NUMERIC(10, 2) DEFAULT NULL, ADD quantity INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sales_items ADD CONSTRAINT FK_175A58FB4A7E4868 FOREIGN KEY (sale_id) REFERENCES sales (id)');
        $this->addSql('CREATE INDEX IDX_175A58FB4A7E4868 ON sales_items (sale_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sales_items DROP FOREIGN KEY FK_175A58FB4A7E4868');
        $this->addSql('DROP INDEX IDX_175A58FB4A7E4868 ON sales_items');
        $this->addSql('ALTER TABLE sales_items DROP sale_id, DROP weigth, DROP price, DROP quantity');
    }
}
