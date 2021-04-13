<?php

namespace App\Entity;

use App\Repository\CVCompetencesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CVCompetencesRepository::class)
 */
class CVCompetences
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CV::class, inversedBy="competences")
     * @ORM\JoinColumn(nullable=false)
     */
    private $CV;

    /**
     * @ORM\ManyToOne(targetEntity=Competence::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $competence;

    /**
     * @ORM\Column(type="integer")
     */
    private $niveau;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCV(): ?CV
    {
        return $this->CV;
    }

    public function setCV(?CV $CV): self
    {
        $this->CV = $CV;

        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): self
    {
        $this->competence = $competence;

        return $this;
    }

    public function getNiveau(): ?int
    {
        return $this->niveau;
    }

    public function setNiveau(int $niveau): self
    {
        if ($niveau >= 1 && $niveau <= 5)
            $this->niveau = $niveau;

        return $this;
    }
}
