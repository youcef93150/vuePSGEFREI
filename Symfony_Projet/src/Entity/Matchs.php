<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "matchs")]
class Matchs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private $id;

    #[ORM\Column(type: "string", length: 100)]
    private $equipeDomicile;

    #[ORM\Column(type: "string", length: 100)]
    private $equipeExterieure;

    #[ORM\Column(type: "datetime")]
    private $dateMatch;

    #[ORM\Column(type: "string", length: 50)]
    private $competition;

    #[ORM\Column(type: "string", length: 200)]
    private $stade;

    #[ORM\Column(type: "integer", nullable: true)]
    private $scoreDomicile = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private $scoreExterieure = null;

    #[ORM\Column(type: "string", length: 20)]
    private $statut = 'programme';

    #[ORM\Column(type: "datetime")]
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // Getters et Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEquipeDomicile(): ?string
    {
        return $this->equipeDomicile;
    }

    public function setEquipeDomicile(string $equipeDomicile): self
    {
        $this->equipeDomicile = $equipeDomicile;
        return $this;
    }

    public function getEquipeExterieure(): ?string
    {
        return $this->equipeExterieure;
    }

    public function setEquipeExterieure(string $equipeExterieure): self
    {
        $this->equipeExterieure = $equipeExterieure;
        return $this;
    }

    public function getDateMatch(): ?\DateTime
    {
        return $this->dateMatch;
    }

    public function setDateMatch(\DateTime $dateMatch): self
    {
        $this->dateMatch = $dateMatch;
        return $this;
    }

    public function getCompetition(): ?string
    {
        return $this->competition;
    }

    public function setCompetition(string $competition): self
    {
        $this->competition = $competition;
        return $this;
    }

    public function getStade(): ?string
    {
        return $this->stade;
    }

    public function setStade(string $stade): self
    {
        $this->stade = $stade;
        return $this;
    }

    public function getScoreDomicile(): ?int
    {
        return $this->scoreDomicile;
    }

    public function setScoreDomicile(?int $scoreDomicile): self
    {
        $this->scoreDomicile = $scoreDomicile;
        return $this;
    }

    public function getScoreExterieure(): ?int
    {
        return $this->scoreExterieure;
    }

    public function setScoreExterieure(?int $scoreExterieure): self
    {
        $this->scoreExterieure = $scoreExterieure;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    // Méthodes utilitaires pour déterminer si PSG joue à domicile ou à l'extérieur
    public function isPsgDomicile(): bool
    {
        return strtoupper($this->equipeDomicile) === 'PSG';
    }

    public function isPsgExterieur(): bool
    {
        return strtoupper($this->equipeExterieure) === 'PSG';
    }

    public function getEquipeAdverse(): ?string
    {
        if ($this->isPsgDomicile()) {
            return $this->equipeExterieure;
        } elseif ($this->isPsgExterieur()) {
            return $this->equipeDomicile;
        }
        return null;
    }

    public function getLieu(): string
    {
        if ($this->isPsgDomicile()) {
            return 'Domicile';
        } elseif ($this->isPsgExterieur()) {
            return 'Extérieur';
        }
        return 'Neutre';
    }

    public function getScorePsg(): ?int
    {
        if ($this->isPsgDomicile()) {
            return $this->scoreDomicile;
        } elseif ($this->isPsgExterieur()) {
            return $this->scoreExterieure;
        }
        return null;
    }

    public function getScoreAdverse(): ?int
    {
        if ($this->isPsgDomicile()) {
            return $this->scoreExterieure;
        } elseif ($this->isPsgExterieur()) {
            return $this->scoreDomicile;
        }
        return null;
    }
}