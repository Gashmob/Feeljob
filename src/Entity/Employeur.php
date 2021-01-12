<?php

namespace App\Entity;

/**
 * Class Employeur
 * @package App\Entity
 */
class Employeur extends GenericUser
{
    /**
     * @var string
     */
    private string $nom_entreprise;
    /**
     * @var string
     */
    private string $adresse;
    /**
     * @var string
     */
    private string $logo;
    /**
     * @var string
     */
    private string $siret;
    /**
     * @var string
     */
    private string $description;
    /**
     * @var string
     */
    private string $telephone;

    /**
     * Employeur constructor.
     * @param string $nom
     * @param string $prenom
     * @param string $nom_entreprise
     * @param string $adresse
     * @param string $logo
     * @param string $siret
     * @param string $description
     * @param string $mail
     * @param string $telephone
     * @param bool $verification
     * @param string $motdepasse
     * @param string $sel
     * @param int|null $id
     */
    public function __construct(string $nom, string $prenom, string $nom_entreprise, string $adresse, string $logo,
                                string $siret, string $description, string $mail, string $telephone, bool $verification,
                                string $motdepasse, string $sel, int $id = null)
    {
        parent::__construct($prenom, $nom, $mail, $verification, $motdepasse, $sel, $id);
        $this->nom_entreprise = $nom_entreprise;
        $this->adresse = $adresse;
        $this->logo = $logo;
        $this->siret = $siret;
        $this->description = $description;
        $this->telephone = $telephone;
    }

    public function flush(): void
    {
    }

    /**
     * @param string $nomEntreprise
     * @return $this
     */
    public function setNomEntreprise(string $nomEntreprise): Employeur
    {
        $this->nom_entreprise = $nomEntreprise;

        return $this;
    }

    /**
     * @param string $adresse
     * @return $this
     */
    public function setAdresse(string $adresse): Employeur
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @param string $logo
     * @return $this
     */
    public function setLogo(string $logo): Employeur
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @param string $siret
     * @return $this
     */
    public function setSiret(string $siret): Employeur
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): Employeur
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param string $telephone
     * @return $this
     */
    public function setTelephone(string $telephone): Employeur
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return string
     */
    public function getNomEntreprise(): string
    {
        return $this->nom_entreprise;
    }

    /**
     * @return string
     */
    public function getAdresse(): string
    {
        return $this->adresse;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @return string
     */
    public function getSiret(): string
    {
        return $this->siret;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTelephone(): string
    {
        return $this->telephone;
    }
}
