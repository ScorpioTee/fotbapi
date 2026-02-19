<?php

namespace App\Entity;

use App\Repository\CompetitionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompetitionRepository::class)]
class Competition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $facrCode = null;

    #[ORM\Column(length: 36, nullable: true)]
    private ?string $req = null;

    #[ORM\ManyToOne(inversedBy: 'competitions')]
    private Season $season;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: CompetitionTable::class, mappedBy: 'competition', cascade: ['persist', 'remove'])]
    private iterable $competitionTables;

    #[ORM\OneToMany(targetEntity: CompetitionMatch::class, mappedBy: 'competition', cascade: ['persist', 'remove'])]
    private iterable $competitionMatches;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFacrCode(): ?string
    {
        return $this->facrCode;
    }

    public function setFacrCode(?string $facrCode): static
    {
        $this->facrCode = $facrCode;

        return $this;
    }

    public function getReq(): ?string
    {
        return $this->req;
    }

    public function setReq(?string $req): static
    {
        $this->req = $req;

        return $this;
    }

    public function getSeason(): Season
    {
        return $this->season;
    }

    public function setSeason(Season $season): static
    {
        $this->season = $season;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt ?? new \DateTimeImmutable('now');

        return $this;
    }

    public function getCompetitionTables(): iterable
    {
        return $this->competitionTables;
    }

    public function setCompetitionTables($competitionTables): static
    {
        $this->competitionTables = $competitionTables;

        return $this;
    }

    public function getCompetitionMatches(): iterable
    {
        return $this->competitionMatches;
    }

    public function setCompetitionMatches($competitionMatches): static
    {
        $this->competitionMatches = $competitionMatches;

        return $this;
    }
}
