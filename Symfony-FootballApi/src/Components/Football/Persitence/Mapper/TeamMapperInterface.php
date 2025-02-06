<?php

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\TeamDto;

interface TeamMapperInterface
{
    public function createTeamDto(array $userData): TeamDto;

    public function getTeamData(TeamDto $teamDto): array;
}
