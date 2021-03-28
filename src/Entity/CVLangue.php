<?php

namespace App\Entity;

use App\Repository\CVLangueRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CVLangueRepository::class)
 */
class CVLangue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CV::class, inversedBy="langues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $CV;

    /**
     * @ORM\ManyToOne(targetEntity=Langue::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $langue;

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

    public function getLangue(): ?Langue
    {
        return $this->langue;
    }

    public function setLangue(?Langue $langue): self
    {
        $this->langue = $langue;

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
