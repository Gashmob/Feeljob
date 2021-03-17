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
     * @ORM\Column(type="datetime")
     */
    private $debut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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
     * @ORM\Column(type="float", nullable=true)
     */
    private $salaire;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deplacement;

    /**
     * @ORM\OneToOne(targetEntity=Adresse::class, cascade={"persist", "remove"})
     */
    private $lieu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $teletravail;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbPostes;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->createdAt = time();
        $this->updatedAt = time();
    }

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

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(?\DateTimeInterface $fin): self
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

    public function setSalaire(?float $salaire): self
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

    public function getLieu(): ?Adresse
    {
        return $this->lieu;
    }

    public function setLieu(?Adresse $lieu): self
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

    public function getNbPostes(): ?int
    {
        return $this->nbPostes;
    }

    public function setNbPostes(int $nbPostes): self
    {
        $this->nbPostes = $nbPostes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
