<?php

namespace App\Components\Football\Persitence\Mapper;

use App\Components\Football\Persitence\DTOs\LeaguesDto;

interface LeaguesMapperInterface
{
    public function createLeaguesDto(array $leaguesData);

    public function getLeaguesData(LeaguesDto $leaguesDto): array;

}