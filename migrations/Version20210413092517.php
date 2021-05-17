<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210413092517 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE abonnement_entreprise (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, description LONGTEXT DEFAULT NULL, montant DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, rue VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, ville VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, adresse_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, description LONGTEXT NOT NULL, date DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, identity INT NOT NULL, UNIQUE INDEX UNIQ_F65593E54DE7DC5C (adresse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE auto_entrepreneur (id INT AUTO_INCREMENT NOT NULL, adresse_id INT NOT NULL, carte_visite_id INT DEFAULT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, nom_entreprise VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, verifie TINYINT(1) NOT NULL, motdepasse VARCHAR(255) NOT NULL, sel VARCHAR(16) NOT NULL, logo VARCHAR(255) NOT NULL, siret VARCHAR(14) NOT NULL, description LONGTEXT NOT NULL, abonne DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, identity INT NOT NULL, UNIQUE INDEX UNIQ_695933674DE7DC5C (adresse_id), UNIQUE INDEX UNIQ_6959336726F61E2E (carte_visite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carte_visite (id INT AUTO_INCREMENT NOT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cv (id INT AUTO_INCREMENT NOT NULL, situation_famille_id INT DEFAULT NULL, naissance DATETIME NOT NULL, permis TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, INDEX IDX_B66FFE92C6A2B2F2 (situation_famille_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cvcompetences (id INT AUTO_INCREMENT NOT NULL, cv_id INT NOT NULL, competence_id INT NOT NULL, niveau INT NOT NULL, INDEX IDX_C79D1224CFE419E2 (cv_id), INDEX IDX_C79D122415761DAB (competence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cvdiplome (id INT AUTO_INCREMENT NOT NULL, cv_id INT NOT NULL, diplome_id INT NOT NULL, date DATETIME NOT NULL, mention VARCHAR(50) NOT NULL, INDEX IDX_9FF8A3E8CFE419E2 (cv_id), INDEX IDX_9FF8A3E826F859E2 (diplome_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cvlangue (id INT AUTO_INCREMENT NOT NULL, cv_id INT NOT NULL, langue_id INT NOT NULL, niveau INT NOT NULL, INDEX IDX_966B490DCFE419E2 (cv_id), INDEX IDX_966B490D2AADBACD (langue_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cvmetier (id INT AUTO_INCREMENT NOT NULL, cv_id INT NOT NULL, metier_id INT NOT NULL, date_debut DATETIME NOT NULL, date_fin DATETIME DEFAULT NULL, INDEX IDX_549C310FCFE419E2 (cv_id), INDEX IDX_549C310FED16FA20 (metier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE diplome (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, etablissement VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employe (id INT AUTO_INCREMENT NOT NULL, adresse_id INT NOT NULL, cv_id INT DEFAULT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, verifie TINYINT(1) NOT NULL, motdepasse VARCHAR(255) NOT NULL, sel VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, identity INT NOT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F804D3B94DE7DC5C (adresse_id), UNIQUE INDEX UNIQ_F804D3B9CFE419E2 (cv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employeur (id INT AUTO_INCREMENT NOT NULL, adresse_id INT NOT NULL, abonnement_id INT DEFAULT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, nom_entreprise VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, verifie TINYINT(1) NOT NULL, motdepasse VARCHAR(255) NOT NULL, sel VARCHAR(16) NOT NULL, logo VARCHAR(255) NOT NULL, siret VARCHAR(14) NOT NULL, description LONGTEXT DEFAULT NULL, abonnement_fin DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, identity INT NOT NULL, UNIQUE INDEX UNIQ_8747E1C74DE7DC5C (adresse_id), INDEX IDX_8747E1C7F1D74413 (abonnement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE langue (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metier (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, nom_entreprise VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, lieu_id INT DEFAULT NULL, nom VARCHAR(50) NOT NULL, debut DATETIME NOT NULL, fin DATETIME DEFAULT NULL, loge TINYINT(1) NOT NULL, heures DOUBLE PRECISION NOT NULL, salaire DOUBLE PRECISION DEFAULT NULL, deplacement TINYINT(1) NOT NULL, teletravail TINYINT(1) NOT NULL, nb_postes INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, identity INT NOT NULL, UNIQUE INDEX UNIQ_132AD0D16AB213CC (lieu_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE particulier (id INT AUTO_INCREMENT NOT NULL, adresse_id INT NOT NULL, prenom VARCHAR(50) NOT NULL, nom VARCHAR(50) NOT NULL, telephone VARCHAR(20) NOT NULL, email VARCHAR(255) NOT NULL, verifie TINYINT(1) NOT NULL, motdepasse VARCHAR(255) NOT NULL, sel VARCHAR(16) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, identity INT NOT NULL, UNIQUE INDEX UNIQ_6CC4D4F34DE7DC5C (adresse_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE realisation (id INT AUTO_INCREMENT NOT NULL, carte_visite_id INT NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_EAA5610E26F61E2E (carte_visite_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE situation_famille (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E54DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE auto_entrepreneur ADD CONSTRAINT FK_695933674DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE auto_entrepreneur ADD CONSTRAINT FK_6959336726F61E2E FOREIGN KEY (carte_visite_id) REFERENCES carte_visite (id)');
        $this->addSql('ALTER TABLE cv ADD CONSTRAINT FK_B66FFE92C6A2B2F2 FOREIGN KEY (situation_famille_id) REFERENCES situation_famille (id)');
        $this->addSql('ALTER TABLE cvcompetences ADD CONSTRAINT FK_C79D1224CFE419E2 FOREIGN KEY (cv_id) REFERENCES cv (id)');
        $this->addSql('ALTER TABLE cvcompetences ADD CONSTRAINT FK_C79D122415761DAB FOREIGN KEY (competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE cvdiplome ADD CONSTRAINT FK_9FF8A3E8CFE419E2 FOREIGN KEY (cv_id) REFERENCES cv (id)');
        $this->addSql('ALTER TABLE cvdiplome ADD CONSTRAINT FK_9FF8A3E826F859E2 FOREIGN KEY (diplome_id) REFERENCES diplome (id)');
        $this->addSql('ALTER TABLE cvlangue ADD CONSTRAINT FK_966B490DCFE419E2 FOREIGN KEY (cv_id) REFERENCES cv (id)');
        $this->addSql('ALTER TABLE cvlangue ADD CONSTRAINT FK_966B490D2AADBACD FOREIGN KEY (langue_id) REFERENCES langue (id)');
        $this->addSql('ALTER TABLE cvmetier ADD CONSTRAINT FK_549C310FCFE419E2 FOREIGN KEY (cv_id) REFERENCES cv (id)');
        $this->addSql('ALTER TABLE cvmetier ADD CONSTRAINT FK_549C310FED16FA20 FOREIGN KEY (metier_id) REFERENCES metier (id)');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B94DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE employe ADD CONSTRAINT FK_F804D3B9CFE419E2 FOREIGN KEY (cv_id) REFERENCES cv (id)');
        $this->addSql('ALTER TABLE employeur ADD CONSTRAINT FK_8747E1C74DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE employeur ADD CONSTRAINT FK_8747E1C7F1D74413 FOREIGN KEY (abonnement_id) REFERENCES abonnement_entreprise (id)');
        $this->addSql('ALTER TABLE offre_emploi ADD CONSTRAINT FK_132AD0D16AB213CC FOREIGN KEY (lieu_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE particulier ADD CONSTRAINT FK_6CC4D4F34DE7DC5C FOREIGN KEY (adresse_id) REFERENCES adresse (id)');
        $this->addSql('ALTER TABLE realisation ADD CONSTRAINT FK_EAA5610E26F61E2E FOREIGN KEY (carte_visite_id) REFERENCES carte_visite (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employeur DROP FOREIGN KEY FK_8747E1C7F1D74413');
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E54DE7DC5C');
        $this->addSql('ALTER TABLE auto_entrepreneur DROP FOREIGN KEY FK_695933674DE7DC5C');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B94DE7DC5C');
        $this->addSql('ALTER TABLE employeur DROP FOREIGN KEY FK_8747E1C74DE7DC5C');
        $this->addSql('ALTER TABLE offre_emploi DROP FOREIGN KEY FK_132AD0D16AB213CC');
        $this->addSql('ALTER TABLE particulier DROP FOREIGN KEY FK_6CC4D4F34DE7DC5C');
        $this->addSql('ALTER TABLE auto_entrepreneur DROP FOREIGN KEY FK_6959336726F61E2E');
        $this->addSql('ALTER TABLE realisation DROP FOREIGN KEY FK_EAA5610E26F61E2E');
        $this->addSql('ALTER TABLE cvcompetences DROP FOREIGN KEY FK_C79D122415761DAB');
        $this->addSql('ALTER TABLE cvcompetences DROP FOREIGN KEY FK_C79D1224CFE419E2');
        $this->addSql('ALTER TABLE cvdiplome DROP FOREIGN KEY FK_9FF8A3E8CFE419E2');
        $this->addSql('ALTER TABLE cvlangue DROP FOREIGN KEY FK_966B490DCFE419E2');
        $this->addSql('ALTER TABLE cvmetier DROP FOREIGN KEY FK_549C310FCFE419E2');
        $this->addSql('ALTER TABLE employe DROP FOREIGN KEY FK_F804D3B9CFE419E2');
        $this->addSql('ALTER TABLE cvdiplome DROP FOREIGN KEY FK_9FF8A3E826F859E2');
        $this->addSql('ALTER TABLE cvlangue DROP FOREIGN KEY FK_966B490D2AADBACD');
        $this->addSql('ALTER TABLE cvmetier DROP FOREIGN KEY FK_549C310FED16FA20');
        $this->addSql('ALTER TABLE cv DROP FOREIGN KEY FK_B66FFE92C6A2B2F2');
        $this->addSql('DROP TABLE abonnement_entreprise');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE auto_entrepreneur');
        $this->addSql('DROP TABLE carte_visite');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE cv');
        $this->addSql('DROP TABLE cvcompetences');
        $this->addSql('DROP TABLE cvdiplome');
        $this->addSql('DROP TABLE cvlangue');
        $this->addSql('DROP TABLE cvmetier');
        $this->addSql('DROP TABLE diplome');
        $this->addSql('DROP TABLE employe');
        $this->addSql('DROP TABLE employeur');
        $this->addSql('DROP TABLE langue');
        $this->addSql('DROP TABLE metier');
        $this->addSql('DROP TABLE offre_emploi');
        $this->addSql('DROP TABLE particulier');
        $this->addSql('DROP TABLE realisation');
        $this->addSql('DROP TABLE situation_famille');
    }
}
