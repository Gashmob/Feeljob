<?php

namespace App\Entity;

use App\Repository\CVRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CVRepository::class)
 */
class CV
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $naissance;

    /**
     * @ORM\Column(type="boolean")
     */
    private $permis;

    /**
     * @ORM\ManyToOne(targetEntity=SituationFamille::class)
     */
    private $situationFamille;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=CVCompetences::class, mappedBy="CV", orphanRemoval=true)
     */
    private $competences;

    /**
     * @ORM\OneToMany(targetEntity=CVDiplome::class, mappedBy="CV", orphanRemoval=true)
     */
    private $diplomes;

    /**
     * @ORM\OneToMany(targetEntity=CVMetier::class, mappedBy="CV", orphanRemoval=true)
     */
    private $metiers;

    /**
     * @ORM\OneToMany(targetEntity=CVLangue::class, mappedBy="CV", orphanRemoval=true)
     */
    private $langues;

    /**
     * @ORM\OneToOne(targetEntity=Employe::class, mappedBy="CV", cascade={"persist", "remove"})
     */
    private $employe;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->langues = new ArrayCollection();
        $this->competences = new ArrayCollection();
        $this->diplomes = new ArrayCollection();
        $this->metiers = new ArrayCollection();

        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNaissance(): ?DateTimeInterface
    {
        return $this->naissance;
    }

    public function setNaissance(DateTimeInterface $naissance): self
    {
        $this->naissance = $naissance;

        return $this;
    }

    public function getPermis(): ?bool
    {
        return $this->permis;
    }

    public function setPermis(bool $permis): self
    {
        $this->permis = $permis;

        return $this;
    }

    public function getSituationFamille(): ?SituationFamille
    {
        return $this->situationFamille;
    }

    public function setSituationFamille(?SituationFamille $situationFamille): self
    {
        $this->situationFamille = $situationFamille;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Langue[]
     */
    public function getLangues(): Collection
    {
        return $this->langues;
    }

    /**
     * @return Collection|CVCompetences[]
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(CVCompetences $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
            $competence->setCV($this);
        }

        return $this;
    }

    public function removeCompetence(CVCompetences $competence): self
    {
        if ($this->competences->removeElement($competence)) {
            // set the owning side to null (unless already changed)
            if ($competence->getCV() === $this) {
                $competence->setCV(null);
            }
        }

        return $this;
    }

    public function clearCompetence(): self
    {
        foreach ($this->competences as $competence) {
            $this->removeCompetence($competence);
        }

        return $this;
    }

    /**
     * @return Collection|CVDiplome[]
     */
    public function getDiplomes(): Collection
    {
        return $this->diplomes;
    }

    public function addDiplome(CVDiplome $diplome): self
    {
        if (!$this->diplomes->contains($diplome)) {
            $this->diplomes[] = $diplome;
            $diplome->setCV($this);
        }

        return $this;
    }

    public function removeDiplome(CVDiplome $diplome): self
    {
        if ($this->diplomes->removeElement($diplome)) {
            // set the owning side to null (unless already changed)
            if ($diplome->getCV() === $this) {
                $diplome->setCV(null);
            }
        }

        return $this;
    }

    public function clearDiplome(): self
    {
        foreach ($this->diplomes as $diplome) {
            $this->removeDiplome($diplome);
        }

        return $this;
    }

    /**
     * @return Collection|CVMetier[]
     */
    public function getMetiers(): Collection
    {
        return $this->metiers;
    }

    public function addMetier(CVMetier $metier): self
    {
        if (!$this->metiers->contains($metier)) {
            $this->metiers[] = $metier;
            $metier->setCV($this);
        }

        return $this;
    }

    public function removeMetier(CVMetier $metier): self
    {
        if ($this->metiers->removeElement($metier)) {
            // set the owning side to null (unless already changed)
            if ($metier->getCV() === $this) {
                $metier->setCV(null);
            }
        }

        return $this;
    }

    public function clearMetier(): self
    {
        foreach ($this->metiers as $metier) {
            $this->removeMetier($metier);
        }

        return $this;
    }

    public function addLangue(CVLangue $langue): self
    {
        if (!$this->langues->contains($langue)) {
            $this->langues[] = $langue;
            $langue->setCV($this);
        }

        return $this;
    }

    public function removeLangue(CVLangue $langue): self
    {
        if ($this->langues->removeElement($langue)) {
            // set the owning side to null (unless already changed)
            if ($langue->getCV() === $this) {
                $langue->setCV(null);
            }
        }

        return $this;
    }

    public function clearLangue(): self
    {
        foreach ($this->langues as $langue) {
            $this->removeLangue($langue);
        }

        return $this;
    }

    public function getEmploye(): ?Employe
    {
        return $this->employe;
    }

    public function setEmploye(?Employe $employe): self
    {
        // unset the owning side of the relation if necessary
        if ($employe === null && $this->employe !== null) {
            $this->employe->setCV(null);
        }

        // set the owning side of the relation if necessary
        if ($employe !== null && $employe->getCV() !== $this) {
            $employe->setCV($this);
        }

        $this->employe = $employe;

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
