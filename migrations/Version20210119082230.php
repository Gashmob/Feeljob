<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210119082230 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auto_entrepreneur ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE candidat ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE entreprise ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE offre_chantier ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE offre_emploi ADD identity INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE auto_entrepreneur DROP identity');
        $this->addSql('ALTER TABLE candidat DROP identity');
        $this->addSql('ALTER TABLE entreprise DROP identity');
        $this->addSql('ALTER TABLE offre_chantier DROP identity');
        $this->addSql('ALTER TABLE offre_emploi DROP identity');
    }
}
