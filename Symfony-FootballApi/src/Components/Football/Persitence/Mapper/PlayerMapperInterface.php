<?php

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\PlayerDto;

interface PlayerMapperInterface
{
    public function createTeamDto(array $playerData): PlayerDto;
    public function getPlayerData(PlayerDto $playerDto): array;
}