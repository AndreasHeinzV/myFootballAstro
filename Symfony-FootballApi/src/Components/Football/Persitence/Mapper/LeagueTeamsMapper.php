<?php

declare(strict_types=1);

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\LeagueTeamsDto;

class LeagueTeamsMapper implements LeagueTeamsMapperInterface
{
    public function createLeagueTeamsDTO(array $leagueData): LeagueTeamsDto
    {
        return new LeagueTeamsDto(
            $leagueData['position'],
            $leagueData['name'],
            $leagueData['teamId'],
            $leagueData['playedGames'],
            $leagueData['won'],
            $leagueData['draw'],
            $leagueData['lost'],
            $leagueData['points'],
            $leagueData['goalsFor'],
            $leagueData['goalsAgainst'],
            $leagueData['goalDifference']
        );
    }

    public function getLeagueTeamsData(LeagueTeamsDto $leagueTeamsDto): array
    {
        return [
            'position' => $leagueTeamsDto->position,
            'name' => $leagueTeamsDto->name,
            'teamId' => $leagueTeamsDto->teamId,
            'playedGames' => $leagueTeamsDto->playedGames,
            'won' => $leagueTeamsDto->won,
            'draw' => $leagueTeamsDto->draw,
            'lost' => $leagueTeamsDto->lost,
            'points' => $leagueTeamsDto->points,
            'goalsFor' => $leagueTeamsDto->goalsFor,
            'goalsAgainst' => $leagueTeamsDto->goalsAgainst,
            'goalDifference' => $leagueTeamsDto->goalDifference,
        ];
    }
}
