<?php

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\LeagueTeamsDto;

interface LeagueTeamsMapperInterface
{
    public function createLeagueTeamsDTO(array $leagueData): LeagueTeamsDto;

    public function getLeagueTeamsData(LeagueTeamsDto $leagueTeamsDto): array;
}
