<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210317162307 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE auto_entrepreneur ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE employe ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE employeur ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE offre_emploi ADD identity INT NOT NULL');
        $this->addSql('ALTER TABLE particulier ADD identity INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP identity');
        $this->addSql('ALTER TABLE auto_entrepreneur DROP identity');
        $this->addSql('ALTER TABLE employe DROP identity');
        $this->addSql('ALTER TABLE employeur DROP identity');
        $this->addSql('ALTER TABLE offre_emploi DROP identity');
        $this->addSql('ALTER TABLE particulier DROP identity');
    }
}
