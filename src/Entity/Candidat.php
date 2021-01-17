<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandidatRepository::class)
 */
class Candidat
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
    private $prenom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $naissance;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $permis;

    /**
     * @ORM\Column(type="integer")
     */
    private $identity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getNaissance(): ?string
    {
        return $this->naissance;
    }

    public function setNaissance(?string $naissance): self
    {
        $this->naissance = $naissance;

        return $this;
    }

    public function getPermis(): ?bool
    {
        return $this->permis;
    }

    public function setPermis(?bool $permis): self
    {
        $this->permis = $permis;

        return $this;
    }

    public function getIdentity(): ?int
    {
        return $this->identity;
    }

    public function setIdentity(int $identity): self
    {
        $this->identity = $identity;

        return $this;
    }
}
