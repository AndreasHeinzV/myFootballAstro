<?php

declare(strict_types=1);

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\LeaguesDto;

class LeaguesMapper implements LeaguesMapperInterface
{
    public function createLeaguesDTO(array $leaguesData): LeaguesDto
    {
        return new LeaguesDto(
            $leaguesData['id'],
            $leaguesData['name'],
            $leaguesData['leagueId']
        );
    }

    public function getLeaguesData(LeaguesDto $leaguesDto): array
    {
        return [
            'id' => $leaguesDto->id,
            'name' => $leaguesDto->name,
            'leagueId' => $leaguesDto->leagueId,
        ];
    }
}
