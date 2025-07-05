<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250309101422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sales_items (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, INDEX IDX_175A58FB12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sales_items ADD CONSTRAINT FK_175A58FB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE sales_item DROP FOREIGN KEY FK_E28CC8CB12469DE2');
        $this->addSql('DROP TABLE sales_item');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sales_item (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, INDEX IDX_E28CC8CB12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sales_item ADD CONSTRAINT FK_E28CC8CB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE sales_items DROP FOREIGN KEY FK_175A58FB12469DE2');
        $this->addSql('DROP TABLE sales_items');
    }
}
