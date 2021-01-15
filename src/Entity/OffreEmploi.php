<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffreEmploiRepository::class)
 */
class OffreEmploi
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $debut;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $fin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $loge;

    /**
     * @ORM\Column(type="float")
     */
    private $heures;

    /**
     * @ORM\Column(type="float")
     */
    private $salaire;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deplacement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $teletravail;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbRecrutement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDebut(): ?string
    {
        return $this->debut;
    }

    public function setDebut(string $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?string
    {
        return $this->fin;
    }

    public function setFin(?string $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getLoge(): ?bool
    {
        return $this->loge;
    }

    public function setLoge(bool $loge): self
    {
        $this->loge = $loge;

        return $this;
    }

    public function getHeures(): ?float
    {
        return $this->heures;
    }

    public function setHeures(float $heures): self
    {
        $this->heures = $heures;

        return $this;
    }

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(float $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getDeplacement(): ?bool
    {
        return $this->deplacement;
    }

    public function setDeplacement(bool $deplacement): self
    {
        $this->deplacement = $deplacement;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getTeletravail(): ?bool
    {
        return $this->teletravail;
    }

    public function setTeletravail(bool $teletravail): self
    {
        $this->teletravail = $teletravail;

        return $this;
    }

    public function getNbRecrutement(): ?int
    {
        return $this->nbRecrutement;
    }

    public function setNbRecrutement(int $nbRecrutement): self
    {
        $this->nbRecrutement = $nbRecrutement;

        return $this;
    }
}
