<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\CompetitionTableRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompetitionTableRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['competition_table:read']],
)]
#[ApiFilter(SearchFilter::class, properties: ['competition' => 'exact'])]
class CompetitionTable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['competition_table:read'])]
    private ?string $club = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?int $position = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?int $win = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?int $draw = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?int $lost = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?int $goalsScored = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?int $goalsReceived = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?int $points = null;

    #[ORM\ManyToOne(inversedBy: 'competitionTables')]
    #[Groups(['competition_table:read'])]
    private ?Competition $competition = null;

    #[ORM\Column]
    #[Groups(['competition_table:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getClub(): ?string
    {
        return $this->club;
    }

    public function setClub(string $club): static
    {
        $this->club = $club;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getWin(): ?int
    {
        return $this->win;
    }

    public function setWin(int $win): static
    {
        $this->win = $win;

        return $this;
    }

    public function getDraw(): ?int
    {
        return $this->draw;
    }

    public function setDraw(int $draw): static
    {
        $this->draw = $draw;

        return $this;
    }

    public function getLost(): ?int
    {
        return $this->lost;
    }

    public function setLost(int $lost): static
    {
        $this->lost = $lost;

        return $this;
    }

    public function getGoalsScored(): ?int
    {
        return $this->goalsScored;
    }

    public function setGoalsScored(int $goalsScored): static
    {
        $this->goalsScored = $goalsScored;

        return $this;
    }

    public function getGoalsReceived(): ?int
    {
        return $this->goalsReceived;
    }

    public function setGoalsReceived(int $goalsReceived): static
    {
        $this->goalsReceived = $goalsReceived;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getCompetition(): Competition
    {
        return $this->competition;
    }

    public function setCompetition(Competition $competition): static
    {
        $this->competition = $competition;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt ?? new \DateTimeImmutable('now');

        return $this;
    }
}
