<?php

declare(strict_types=1);

namespace App\Components\Football\Business;

use App\Components\Api\ApiRequestFacadeInterface;
use App\Components\Football\Persitence\DTOs\PlayerDto;

readonly class FootballBusinessFacade implements FootballBusinessFacadeInterface
{
    public function __construct(private ApiRequestFacadeInterface $apiRequestFacade)
    {
    }

    public function getLeagues(): array
    {
        return $this->apiRequestFacade->getLeagues();
    }

    public function getLeagueTeams(string $code): array
    {
        return $this->apiRequestFacade->getLeagueTeams($code);
    }

    public function getTeam(int $id): array
    {
        return $this->apiRequestFacade->getTeam($id);
    }

    public function getPlayer(string $id): ?PlayerDto
    {
        return $this->apiRequestFacade->getPlayer($id);
    }
}
