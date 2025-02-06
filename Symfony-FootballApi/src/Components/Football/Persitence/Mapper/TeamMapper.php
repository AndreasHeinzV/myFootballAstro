<?php

declare(strict_types=1);

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\TeamDto;

class TeamMapper implements TeamMapperInterface
{
    public function createTeamDto(array $userData): TeamDto
    {
        return new TeamDto(
            $userData['playerID'],
            $userData['link'],
            $userData['name']
        );
    }

    public function getTeamData(TeamDto $teamDto): array
    {
        return [
            'playerID' => $teamDto->playerID,
            'link' => $teamDto->link,
            'name' => $teamDto->name,
        ];
    }
}
