<?php

namespace App\Components\Football\Business;

use App\Components\Football\Persitence\DTOs\PlayerDto;

interface FootballBusinessFacadeInterface
{
    public function getLeagues(): array;

    public function getLeagueTeams(string $code): array;

    public function getTeam(int $id): array;

    public function getPlayer(string $id): ?PlayerDto;
}