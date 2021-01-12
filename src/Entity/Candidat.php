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
     */
    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
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
     */
    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function flush(): void
    {
        $result = (new PreparedQuery('MERGE (c:Candidat {nom:$nom, prenom:$prenom, telephone:$telephone, mail:$mail, verification:$verification, motdepasse:$motdepasse, sel:$sel}) RETURN id(c) AS id'))
            ->setString('nom', $this->getNom())
            ->setString('prenom', $this->getPrenom())
            ->setString('telephone', $this->telephone)
            ->setString('mail', $this->getMail())
            ->setBoolean('verification', $this->isVerification())
            ->setString('motdepasse', $this->getMotdepasse())
            ->setString('sel', $this->getSel())
            ->run()
            ->getOneOrNullResult();

        $this->id = $result['id'];
    }
}