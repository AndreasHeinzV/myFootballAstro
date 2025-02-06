<?php

namespace App\Components\Api\Business\Model;

use App\Components\Football\Persitence\DTOs\PlayerDto;

interface ApiRequesterInterface
{
    public function apiRequest(string $url): array;

    public function getTeam(int $id): array;

    public function getLeagueTeams(string $code): array;

    public function getLeagues(): array;

    public function getPlayer(string $playerId): ?PlayerDto;
}
