<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230312091722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE company_history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE company_history (id INT NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, open INT NOT NULL, high INT NOT NULL, low INT NOT NULL, close INT NOT NULL, volume INT NOT NULL, adjclose INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN company_history.date IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE company_history_id_seq CASCADE');
        $this->addSql('DROP TABLE company_history');
    }
}
