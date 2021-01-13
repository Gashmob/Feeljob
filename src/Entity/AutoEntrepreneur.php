<?php


namespace App\Entity;


use App\database\PreparedQuery;

/**
 * Class AutoEntrepreneur
 * @package App\Entity
 */
class AutoEntrepreneur extends GenericUser
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
     * @var string
     */
    private string $carte;
    /**
     * @var bool
     */
    private bool $abonne;
    /**
     * @var array
     */
    private array $activitees;

    public function __construct(string $prenom, string $nom, string $mail, bool $verification, string $motdepasse,
                                string $sel, string $nom_entreprise, string $adresse, string $logo, string $siret,
                                string $description, string $telephone, string $carte, bool $abonne, array $activitees,
                                int $id = null)
    {
        parent::__construct($prenom, $nom, $mail, $verification, $motdepasse, $sel, $id);
        $this->nom_entreprise = $nom_entreprise;
        $this->adresse = $adresse;
        $this->logo = $logo;
        $this->siret = $siret;
        $this->description = $description;
        $this->telephone = $telephone;
        $this->carte = $carte;
        $this->abonne = $abonne;
        $this->activitees = $activitees;
    }

    /**
     * @return string
     */
    public function getNomEntreprise(): string
    {
        return $this->nom_entreprise;
    }

    /**
     * @param string $nom_entreprise
     * @return AutoEntrepreneur
     */
    public function setNomEntreprise(string $nom_entreprise): AutoEntrepreneur
    {
        $this->nom_entreprise = $nom_entreprise;

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
     * @return AutoEntrepreneur
     */
    public function setAdresse(string $adresse): AutoEntrepreneur
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     * @return AutoEntrepreneur
     */
    public function setLogo(string $logo): AutoEntrepreneur
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return string
     */
    public function getSiret(): string
    {
        return $this->siret;
    }

    /**
     * @param string $siret
     * @return AutoEntrepreneur
     */
    public function setSiret(string $siret): AutoEntrepreneur
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return AutoEntrepreneur
     */
    public function setDescription(string $description): AutoEntrepreneur
    {
        $this->description = $description;

        return $this;
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
     * @return AutoEntrepreneur
     */
    public function setTelephone(string $telephone): AutoEntrepreneur
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return string
     */
    public function getCarte(): string
    {
        return $this->carte;
    }

    /**
     * @param string $carte
     * @return AutoEntrepreneur
     */
    public function setCarte(string $carte): AutoEntrepreneur
    {
        $this->carte = $carte;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAbonne(): bool
    {
        return $this->abonne;
    }

    /**
     * @param bool $abonne
     * @return AutoEntrepreneur
     */
    public function setAbonne(bool $abonne): AutoEntrepreneur
    {
        $this->abonne = $abonne;

        return $this;
    }

    /**
     * @return array
     */
    public function getActivitees(): array
    {
        return $this->activitees;
    }

    /**
     * @param string $activite
     * @return $this
     */
    public function addActivite(string $activite): AutoEntrepreneur
    {
        if (!in_array($activite, $this->activitees)) {
            $this->activitees[] = $activite;
        }

        return $this;
    }

    /**
     * @param string $activite
     * @return $this
     */
    public function removeActivite(string $activite): AutoEntrepreneur
    {
        if (in_array($activite, $this->activitees)) {
            unset($this->activitees[array_search($activite, $this->activitees)]);
        }

        return $this;
    }

    public function flush(): void
    {
        if ($this->id != null) { // Si l'id est déjà set
            if ((new PreparedQuery('MATCH (a:AutoEntrepreneur) WHERE ID(a) = $id RETURN a'))
                    ->setInteger('id', $this->id)
                    ->run()->getOneOrNullResult() != null) { // Si le node existe déjà
                // Update les valeurs
                (new PreparedQuery('MATCH (a:AutoEntrepreneur) WHERE ID(a) = $id SET a.nom=$nom, a.prenom=$prenom, a.nomEntreprise=$nomEntreprise, a.adresse=$adresse, a.logo=$logo, a.carte=$carte, a.description=$description, a.mail=$mail, a.verification=$verification, a.motdepasse=$motdepasse, a.sel=$sel, a.abonne=$abonne, a.siret=$siret, a.telephone=$telephone'))
                    ->setInteger('id', $this->id)
                    ->setString('nom', $this->getNom())
                    ->setString('prenom', $this->getPrenom())
                    ->setString('nomEntreprise', $this->nom_entreprise)
                    ->setString('adresse', $this->adresse)
                    ->setString('logo', $this->logo)
                    ->setString('carte', $this->carte)
                    ->setString('description', $this->description)
                    ->setString('mail', $this->getMail())
                    ->setBoolean('verification', $this->isVerification())
                    ->setString('motdepasse', $this->getMotdepasse())
                    ->setString('sel', $this->getSel())
                    ->setBoolean('abonne', $this->abonne)
                    ->setString('siret', $this->siret)
                    ->setString('telephone', $this->telephone)
                    ->run();
                return;
            }
        }

        $result = (new PreparedQuery('CREATE (a:AutoEntrepreneur {nom:$nom, prenom:$prenom, nomEntreprise:$nomEntreprise, adresse:$adresse, logo:$logo, carte:$carte, description:$description, mail:$mail, verification:$verification, motdepasse:$motdepasse, sel:$sel, abonne:$abonne, siret:$siret, telephone:$telephone}) RETURN ID(a) as id'))
            ->setString('nom', $this->getNom())
            ->setString('prenom', $this->getPrenom())
            ->setString('nomEntreprise', $this->nom_entreprise)
            ->setString('adresse', $this->adresse)
            ->setString('logo', $this->logo)
            ->setString('carte', $this->carte)
            ->setString('description', $this->description)
            ->setString('mail', $this->getMail())
            ->setBoolean('verification', $this->isVerification())
            ->setString('motdepasse', $this->getMotdepasse())
            ->setString('sel', $this->getSel())
            ->setBoolean('abonne', $this->abonne)
            ->setString('siret', $this->siret)
            ->setString('telephone', $this->telephone)
            ->run()->getOneOrNullResult();

        $this->id = $result['id'];
    }


}