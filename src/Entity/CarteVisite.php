<?php

namespace App\Entity;

use App\Repository\CarteVisiteRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CarteVisiteRepository::class)
 */
class CarteVisite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Realisation::class, mappedBy="carteVisite", orphanRemoval=true)
     */
    private $realisations;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToOne(targetEntity=AutoEntrepreneur::class, mappedBy="carteVisite", cascade={"persist", "remove"})
     */
    private $autoEntrepreneur;

    public function __construct()
    {
        $this->realisations = new ArrayCollection();
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Realisation[]
     */
    public function getRealisations(): Collection
    {
        return $this->realisations;
    }

    public function addRealisation(Realisation $realisation): self
    {
        if (!$this->realisations->contains($realisation)) {
            $this->realisations[] = $realisation;
            $realisation->setCarteVisite($this);
        }

        return $this;
    }

    public function removeRealisation(Realisation $realisation): self
    {
        if ($this->realisations->removeElement($realisation)) {
            // set the owning side to null (unless already changed)
            if ($realisation->getCarteVisite() === $this) {
                $realisation->setCarteVisite(null);
            }
        }

        return $this;
    }

    public function clearRealisation(): self
    {
        foreach ($this->realisations as $realisation) {
            $this->removeRealisation($realisation);
        }

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

    public function getAutoEntrepreneur(): ?AutoEntrepreneur
    {
        return $this->autoEntrepreneur;
    }

    public function setAutoEntrepreneur(?AutoEntrepreneur $autoEntrepreneur): self
    {
        // unset the owning side of the relation if necessary
        if ($autoEntrepreneur === null && $this->autoEntrepreneur !== null) {
            $this->autoEntrepreneur->setCarteVisite(null);
        }

        // set the owning side of the relation if necessary
        if ($autoEntrepreneur !== null && $autoEntrepreneur->getCarteVisite() !== $this) {
            $autoEntrepreneur->setCarteVisite($this);
        }

        $this->autoEntrepreneur = $autoEntrepreneur;

        return $this;
    }
}
