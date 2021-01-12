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
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
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
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
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
     */
    public function setMail(string $mail): void
    {
        $this->mail = $mail;
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
     */
    public function setVerification(bool $verification): void
    {
        $this->verification = $verification;
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
     */
    public function setMotdepasse(string $motdepasse): void
    {
        $this->motdepasse = $motdepasse;
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
     */
    public function setSel(string $sel): void
    {
        $this->sel = $sel;
    }

    public function flush(): void
    {

    }
}