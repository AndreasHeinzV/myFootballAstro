<?php

namespace App\Entity;

use App\Components\UserFavorite\Persistence\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $favoritePosition = null;

    #[ORM\Column]
    private ?int $teamId = null;

    #[ORM\Column(length: 255)]
    private ?string $teamName = null;

    #[ORM\Column(length: 255)]
    private ?string $TeamCrest = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFavoritePosition(): ?int
    {
        return $this->favoritePosition;
    }

    public function setFavoritePosition(int $favoritePosition): static
    {
        $this->favoritePosition = $favoritePosition;

        return $this;
    }

    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    public function setTeamId(int $teamId): static
    {
        $this->teamId = $teamId;

        return $this;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function setTeamName(string $teamName): static
    {
        $this->teamName = $teamName;

        return $this;
    }

    public function getTeamCrest(): ?string
    {
        return $this->TeamCrest;
    }

    public function setTeamCrest(string $TeamCrest): static
    {
        $this->TeamCrest = $TeamCrest;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
