<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250125004941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE competition_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE competition_match_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE competition_table_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE season_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE competition (id INT NOT NULL, season_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, facr_code VARCHAR(255) DEFAULT NULL, req VARCHAR(36) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B50A2CB14EC001D1 ON competition (season_id)');
        $this->addSql('COMMENT ON COLUMN competition.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE competition_match (id INT NOT NULL, competition_id INT DEFAULT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, home VARCHAR(255) NOT NULL, away VARCHAR(255) NOT NULL, home_score INT DEFAULT NULL, away_score INT DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_89ECF7D37B39D312 ON competition_match (competition_id)');
        $this->addSql('COMMENT ON COLUMN competition_match.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE competition_table (id INT NOT NULL, competition_id INT DEFAULT NULL, club VARCHAR(255) NOT NULL, position INT NOT NULL, win INT NOT NULL, draw INT NOT NULL, lost INT NOT NULL, goals_scored INT NOT NULL, goals_received INT NOT NULL, points INT NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_59EBD907B39D312 ON competition_table (competition_id)');
        $this->addSql('COMMENT ON COLUMN competition_table.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE season (id INT NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN season.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE competition ADD CONSTRAINT FK_B50A2CB14EC001D1 FOREIGN KEY (season_id) REFERENCES season (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE competition_match ADD CONSTRAINT FK_89ECF7D37B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE competition_table ADD CONSTRAINT FK_59EBD907B39D312 FOREIGN KEY (competition_id) REFERENCES competition (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE competition_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE competition_match_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE competition_table_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE season_id_seq CASCADE');
        $this->addSql('ALTER TABLE competition DROP CONSTRAINT FK_B50A2CB14EC001D1');
        $this->addSql('ALTER TABLE competition_match DROP CONSTRAINT FK_89ECF7D37B39D312');
        $this->addSql('ALTER TABLE competition_table DROP CONSTRAINT FK_59EBD907B39D312');
        $this->addSql('DROP TABLE competition');
        $this->addSql('DROP TABLE competition_match');
        $this->addSql('DROP TABLE competition_table');
        $this->addSql('DROP TABLE season');
    }
}
