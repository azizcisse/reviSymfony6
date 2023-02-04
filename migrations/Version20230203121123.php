<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230203121123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job ADD personne_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8A21BD112 FOREIGN KEY (personne_id) REFERENCES personne (id)');
        $this->addSql('CREATE INDEX IDX_FBD8E0F8A21BD112 ON job (personne_id)');
        $this->addSql('ALTER TABLE personne CHANGE firstname firstname VARCHAR(50) NOT NULL, CHANGE name name VARCHAR(50) NOT NULL, CHANGE age age SMALLINT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8A21BD112');
        $this->addSql('DROP INDEX IDX_FBD8E0F8A21BD112 ON job');
        $this->addSql('ALTER TABLE job DROP personne_id');
        $this->addSql('ALTER TABLE personne CHANGE firstname firstname VARCHAR(50) DEFAULT NULL, CHANGE name name VARCHAR(50) DEFAULT NULL, CHANGE age age SMALLINT DEFAULT NULL');
    }
}
