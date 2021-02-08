<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210208060343 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_695933676A95E9C4 ON auto_entrepreneur (identity)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA606A95E9C4 ON entreprise (identity)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_58ADDC4D6A95E9C4 ON offre_chantier (identity)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_132AD0D16A95E9C4 ON offre_emploi (identity)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_695933676A95E9C4 ON auto_entrepreneur');
        $this->addSql('DROP INDEX UNIQ_D19FA606A95E9C4 ON entreprise');
        $this->addSql('DROP INDEX UNIQ_58ADDC4D6A95E9C4 ON offre_chantier');
        $this->addSql('DROP INDEX UNIQ_132AD0D16A95E9C4 ON offre_emploi');
    }
}
