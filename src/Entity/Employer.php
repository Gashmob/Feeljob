<?php

namespace App\Entity;

class Employer extends Entity
{


    private string $nom;
    private string $prenom;
    private string $nom_entreprise;
    private string $adresse;
    private string $logo;
    private string $siret;
    private string $description;
    private string $mail;
    private string $telephone;
    private bool $verification;
    private string $motdepasse;
    private string $sel;

    public function __construct($nom, $prenom, $nom_entreprise, $adresse, $logo, $siret, $description, $mail, $telephone, $verification, $motdepasse, $sel)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->nom_entreprise = $nom_entreprise;
        $this->adresse = $adresse;
        $this->logo = $logo;
        $this->siret = $siret;
        $this->description = $description;
        $this->mail = $mail;
        $this->telephone = $telephone;
        $this->verification = $verification;
        $this->motdepasse = $motdepasse;
        $this->sel = $sel;
    }

    public function flush(): void
    {
    }

    function setNom($nom)
    {
        $this->nom = $nom;
    }

    function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    function setNomentreprise($nomentreprise)
    {
        $this->nomentreprise = $nomentreprise;
    }

    function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    function setLogo($logo)
    {
        $this->logo = $logo;
    }

    function setSiret($siret)
    {
        $this->siret = $siret;
    }

    function setDescription($description)
    {
        $this->description = $description;
    }

    function setMail($mail)
    {
        $this->mail = $mail;
    }

    function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    function setVerification($verification)
    {
        $this->verification = $verification;
    }

    function setMotdepasse($motdepasse)
    {
        $this->motdepasse = $motdepasse;
    }

    function setSel($sel)
    {
        $this->sel = $sel;
    }
}
