<?php


namespace App\Entity;


use App\database\PreparedQuery;

/**
 * Class Candidat
 * @package App\Entity
 */
class Candidat extends GenericUser
{
    /**
     * @var string
     */
    private string $telephone;
    /**
     * @var string
     */
    private string $adresse;

    public function __construct(string $prenom, string $nom, string $mail, bool $verification, string $telephone,
                                string $adresse, string $motdepasse, string $sel, int $id = null)
    {
        parent::__construct($prenom, $nom, $mail, $verification, $motdepasse, $sel, $id);
        $this->telephone = $telephone;
        $this->adresse = $adresse;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     * @return Candidat
     */
    public function setTelephone(string $telephone): Candidat
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdresse(): string
    {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     * @return Candidat
     */
    public function setAdresse(string $adresse): Candidat
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function flush(): void
    {
        if ($this->id != null) { // Si l'id est déjà set
            if ((new PreparedQuery('MATCH (c:Candidat) WHERE ID(c) = $id RETURN c')) // Si le node existe déjà
                ->setInteger('id', $this->id)
                    ->run()->getOneOrNullResult() != null) {
                // Update les valeurs
                (new PreparedQuery('MATCH (c:Candidat) WHERE ID(c) = 36 SET c.nom=$nom, c.prenom=$prenom, c.telephone=$telephone, c.adresse=$adresse, c.mail=$mail, c.verification=$verification, c.motdepasse=$motdepasse, c.sel=$sel'))
                    ->setString('nom', $this->getNom())
                    ->setString('prenom', $this->getPrenom())
                    ->setString('telephone', $this->telephone)
                    ->setString('adresse', $this->adresse)
                    ->setString('mail', $this->getMail())
                    ->setBoolean('verification', $this->isVerification())
                    ->setString('motdepasse', $this->getMotdepasse())
                    ->setString('sel', $this->getSel())
                    ->run();

                return;
            }
        }
        // Sinon création du nouveau node
        $result = (new PreparedQuery('CREATE (c:Candidat {nom:$nom, prenom:$prenom, telephone:$telephone, mail:$mail, verification:$verification, motdepasse:$motdepasse, sel:$sel}) RETURN id(c) AS id'))
            ->setString('nom', $this->getNom())
            ->setString('prenom', $this->getPrenom())
            ->setString('telephone', $this->telephone)
            ->setString('mail', $this->getMail())
            ->setBoolean('verification', $this->isVerification())
            ->setString('motdepasse', $this->getMotdepasse())
            ->setString('sel', $this->getSel())
            ->run()->getOneOrNullResult();

        $this->id = $result['id'];
    }
}