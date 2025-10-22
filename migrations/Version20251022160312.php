<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251022160312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cash_movements (id INT AUTO_INCREMENT NOT NULL, made_by_id INT DEFAULT NULL, cash_register_session_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', amount NUMERIC(10, 2) NOT NULL, comment LONGTEXT DEFAULT NULL, type VARCHAR(10) NOT NULL, INDEX IDX_DD9448DD90B9D269 (made_by_id), INDEX IDX_DD9448DD6C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cash_register_closures (id INT AUTO_INCREMENT NOT NULL, closed_by_id INT DEFAULT NULL, cash_register_session_id INT NOT NULL, closed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closing_cash_amount DOUBLE PRECISION NOT NULL, discrepancy NUMERIC(10, 2) NOT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_9656D5E9E1FA7797 (closed_by_id), INDEX IDX_9656D5E96C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cash_register_sessions (id INT AUTO_INCREMENT NOT NULL, opened_by_id INT DEFAULT NULL, opening_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', cash_float DOUBLE PRECISION NOT NULL, INDEX IDX_E6A4623EAB159F5 (opened_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE donations (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, category_id INT NOT NULL, weight DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CDE98962A76ED395 (user_id), INDEX IDX_CDE9896212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sales (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, cash_register_session_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', total_price NUMERIC(10, 2) NOT NULL, cash_amount NUMERIC(10, 2) DEFAULT NULL, card_amount NUMERIC(10, 2) DEFAULT NULL, zipcode_customer VARCHAR(12) DEFAULT NULL, change_amount NUMERIC(10, 2) DEFAULT NULL, pwyw_amount NUMERIC(10, 2) DEFAULT NULL, customer_city VARCHAR(255) DEFAULT NULL, INDEX IDX_6B817044A76ED395 (user_id), INDEX IDX_6B8170446C172579 (cash_register_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sales_items (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, sale_id INT NOT NULL, weight NUMERIC(10, 2) DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, quantity INT DEFAULT NULL, INDEX IDX_175A58FB12469DE2 (category_id), INDEX IDX_175A58FB4A7E4868 (sale_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE visitors (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, count INT NOT NULL, date DATE NOT NULL, INDEX IDX_7B74A43FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE volunteer_recurrences (id INT AUTO_INCREMENT NOT NULL, frequency VARCHAR(255) NOT NULL, until_date DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE volunteer_registrations (id INT AUTO_INCREMENT NOT NULL, session_id INT NOT NULL, user_id INT NOT NULL, status VARCHAR(20) NOT NULL, registered_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CD9BA599613FECDF (session_id), INDEX IDX_CD9BA599A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE volunteer_sessions (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, recurrence_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, start_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', end_datetime DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', required_volunteers INT DEFAULT NULL, is_cancelled TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B2BCCD92B03A8386 (created_by_id), INDEX IDX_B2BCCD922C414CE8 (recurrence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD90B9D269 FOREIGN KEY (made_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cash_movements ADD CONSTRAINT FK_DD9448DD6C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE cash_register_closures ADD CONSTRAINT FK_9656D5E9E1FA7797 FOREIGN KEY (closed_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cash_register_closures ADD CONSTRAINT FK_9656D5E96C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE cash_register_sessions ADD CONSTRAINT FK_E6A4623EAB159F5 FOREIGN KEY (opened_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE donations ADD CONSTRAINT FK_CDE98962A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE donations ADD CONSTRAINT FK_CDE9896212469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE sales ADD CONSTRAINT FK_6B817044A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sales ADD CONSTRAINT FK_6B8170446C172579 FOREIGN KEY (cash_register_session_id) REFERENCES cash_register_sessions (id)');
        $this->addSql('ALTER TABLE sales_items ADD CONSTRAINT FK_175A58FB12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE sales_items ADD CONSTRAINT FK_175A58FB4A7E4868 FOREIGN KEY (sale_id) REFERENCES sales (id)');
        $this->addSql('ALTER TABLE visitors ADD CONSTRAINT FK_7B74A43FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE volunteer_registrations ADD CONSTRAINT FK_CD9BA599613FECDF FOREIGN KEY (session_id) REFERENCES volunteer_sessions (id)');
        $this->addSql('ALTER TABLE volunteer_registrations ADD CONSTRAINT FK_CD9BA599A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE volunteer_sessions ADD CONSTRAINT FK_B2BCCD92B03A8386 FOREIGN KEY (created_by_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE volunteer_sessions ADD CONSTRAINT FK_B2BCCD922C414CE8 FOREIGN KEY (recurrence_id) REFERENCES volunteer_recurrences (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD90B9D269');
        $this->addSql('ALTER TABLE cash_movements DROP FOREIGN KEY FK_DD9448DD6C172579');
        $this->addSql('ALTER TABLE cash_register_closures DROP FOREIGN KEY FK_9656D5E9E1FA7797');
        $this->addSql('ALTER TABLE cash_register_closures DROP FOREIGN KEY FK_9656D5E96C172579');
        $this->addSql('ALTER TABLE cash_register_sessions DROP FOREIGN KEY FK_E6A4623EAB159F5');
        $this->addSql('ALTER TABLE donations DROP FOREIGN KEY FK_CDE98962A76ED395');
        $this->addSql('ALTER TABLE donations DROP FOREIGN KEY FK_CDE9896212469DE2');
        $this->addSql('ALTER TABLE sales DROP FOREIGN KEY FK_6B817044A76ED395');
        $this->addSql('ALTER TABLE sales DROP FOREIGN KEY FK_6B8170446C172579');
        $this->addSql('ALTER TABLE sales_items DROP FOREIGN KEY FK_175A58FB12469DE2');
        $this->addSql('ALTER TABLE sales_items DROP FOREIGN KEY FK_175A58FB4A7E4868');
        $this->addSql('ALTER TABLE visitors DROP FOREIGN KEY FK_7B74A43FA76ED395');
        $this->addSql('ALTER TABLE volunteer_registrations DROP FOREIGN KEY FK_CD9BA599613FECDF');
        $this->addSql('ALTER TABLE volunteer_registrations DROP FOREIGN KEY FK_CD9BA599A76ED395');
        $this->addSql('ALTER TABLE volunteer_sessions DROP FOREIGN KEY FK_B2BCCD92B03A8386');
        $this->addSql('ALTER TABLE volunteer_sessions DROP FOREIGN KEY FK_B2BCCD922C414CE8');
        $this->addSql('DROP TABLE cash_movements');
        $this->addSql('DROP TABLE cash_register_closures');
        $this->addSql('DROP TABLE cash_register_sessions');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE donations');
        $this->addSql('DROP TABLE sales');
        $this->addSql('DROP TABLE sales_items');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE visitors');
        $this->addSql('DROP TABLE volunteer_recurrences');
        $this->addSql('DROP TABLE volunteer_registrations');
        $this->addSql('DROP TABLE volunteer_sessions');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
