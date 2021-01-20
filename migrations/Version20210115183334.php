<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115183334 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auto_entrepreneur (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, telephone VARCHAR(16) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, carte VARCHAR(255) DEFAULT NULL, abonne TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidat (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, telephone VARCHAR(16) DEFAULT NULL, naissance VARCHAR(10) DEFAULT NULL, permis TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, nom_entreprise VARCHAR(255) NOT NULL, telephone VARCHAR(16) DEFAULT NULL, logo VARCHAR(255) DEFAULT NULL, siret VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_chantier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, date VARCHAR(10) NOT NULL, adresse VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, debut VARCHAR(20) NOT NULL, fin VARCHAR(20) DEFAULT NULL, loge TINYINT(1) NOT NULL, heures DOUBLE PRECISION NOT NULL, salaire DOUBLE PRECISION NOT NULL, deplacement TINYINT(1) NOT NULL, lieu VARCHAR(255) DEFAULT NULL, teletravail TINYINT(1) NOT NULL, nb_recrutement INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auto_entrepreneur');
        $this->addSql('DROP TABLE candidat');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE offre_chantier');
        $this->addSql('DROP TABLE offre_emploi');
    }
}
