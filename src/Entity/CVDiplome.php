<?php

namespace App\Entity;

use App\Repository\CVDiplomeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CVDiplomeRepository::class)
 */
class CVDiplome
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=CV::class, inversedBy="diplomes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $CV;

    /**
     * @ORM\ManyToOne(targetEntity=Diplome::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $diplome;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $mention;

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

    public function getDiplome(): ?Diplome
    {
        return $this->diplome;
    }

    public function setDiplome(?Diplome $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(string $mention): self
    {
        $this->mention = $mention;

        return $this;
    }
}
