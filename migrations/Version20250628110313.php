<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250628110313 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_register_closure DROP FOREIGN KEY FK_5017171FA76ED395A76ED395');
        $this->addSql('DROP INDEX IDX_5017171FA76ED395 ON cash_register_closure');
        $this->addSql('ALTER TABLE cash_register_closure CHANGE user_id closed_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cash_register_closure ADD CONSTRAINT FK_5017171FE1FA7797E1FA7797 FOREIGN KEY (closed_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5017171FE1FA7797 ON cash_register_closure (closed_by_id)');
        $this->addSql('ALTER TABLE cash_register_sessions DROP FOREIGN KEY FK_E6A4623EA76ED395A76ED395');
        $this->addSql('DROP INDEX IDX_E6A4623EA76ED395 ON cash_register_sessions');
        $this->addSql('ALTER TABLE cash_register_sessions CHANGE user_id opened_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cash_register_sessions ADD CONSTRAINT FK_E6A4623EAB159F5AB159F5 FOREIGN KEY (opened_by_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_E6A4623EAB159F5 ON cash_register_sessions (opened_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_register_closure DROP FOREIGN KEY FK_5017171FE1FA7797E1FA7797');
        $this->addSql('DROP INDEX IDX_5017171FE1FA7797 ON cash_register_closure');
        $this->addSql('ALTER TABLE cash_register_closure CHANGE closed_by_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cash_register_closure ADD CONSTRAINT FK_5017171FA76ED395A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5017171FA76ED395 ON cash_register_closure (user_id)');
        $this->addSql('ALTER TABLE cash_register_sessions DROP FOREIGN KEY FK_E6A4623EAB159F5AB159F5');
        $this->addSql('DROP INDEX IDX_E6A4623EAB159F5 ON cash_register_sessions');
        $this->addSql('ALTER TABLE cash_register_sessions CHANGE opened_by_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cash_register_sessions ADD CONSTRAINT FK_E6A4623EA76ED395A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_E6A4623EA76ED395 ON cash_register_sessions (user_id)');
    }
}
