<?php


namespace App\Entity;


class GenericUser extends Entity
{
    /**
     * @var string
     */
    private string $prenom;
    /**
     * @var string
     */
    private string $nom;
    /**
     * @var string
     */
    private string $mail;
    /**
     * @var bool
     */
    private bool $verification;
    /**
     * @var string
     */
    private string $motdepasse;
    /**
     * @var string
     */
    private string $sel;

    /**
     * GenericUser constructor.
     * @param string $prenom
     * @param string $nom
     * @param string $mail
     * @param bool $verification
     * @param string $motdepasse
     * @param string $sel
     * @param int|null $id
     */
    public function __construct(string $prenom, string $nom, string $mail, bool $verification, string $motdepasse, string $sel, int $id = null)
    {
        parent::__construct($id);
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->mail = $mail;
        $this->verification = $verification;
        $this->motdepasse = $motdepasse;
        $this->sel = $sel;
    }

    /**
     * @return string
     */
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     * @return GenericUser
     */
    public function setPrenom(string $prenom): GenericUser
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return GenericUser
     */
    public function setNom(string $nom): GenericUser
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     * @return GenericUser
     */
    public function setMail(string $mail): GenericUser
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return bool
     */
    public function isVerification(): bool
    {
        return $this->verification;
    }

    /**
     * @param bool $verification
     * @return GenericUser
     */
    public function setVerification(bool $verification): GenericUser
    {
        $this->verification = $verification;

        return $this;
    }

    /**
     * @return string
     */
    public function getMotdepasse(): string
    {
        return $this->motdepasse;
    }

    /**
     * @param string $motdepasse
     * @return GenericUser
     */
    public function setMotdepasse(string $motdepasse): GenericUser
    {
        $this->motdepasse = $motdepasse;

        return $this;
    }

    /**
     * @return string
     */
    public function getSel(): string
    {
        return $this->sel;
    }

    /**
     * @param string $sel
     * @return GenericUser
     */
    public function setSel(string $sel): GenericUser
    {
        $this->sel = $sel;

        return $this;
    }

    public function flush(): void
    {

    }
}