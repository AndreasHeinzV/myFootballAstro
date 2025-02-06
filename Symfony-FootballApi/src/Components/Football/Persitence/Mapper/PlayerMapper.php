<?php

declare(strict_types=1);

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\PlayerDto;

class PlayerMapper implements PlayerMapperInterface
{
    public function createTeamDto(array $playerData): PlayerDto
    {
        return new PlayerDto(
            $playerData['name'],
            $playerData['position'],
            $playerData['dateOfBirth'],
            $playerData['nationality'],
            $playerData['shirtNumber']
        );
    }

    public function getPlayerData(PlayerDto $playerDto): array
    {
        return [
            'playerName' => $playerDto->name,
            'playerPosition' => $playerDto->position,
            'playerDateOfBirth' => $playerDto->dateOfBirth,
            'playerNationality' => $playerDto->nationality,
            'playerShirtNumber' => $playerDto->shirtNumber,
        ];
    }
}
