<?php

declare(strict_types=1);

namespace App\Components\Api;

use App\Components\Api\Business\Model\ApiRequesterInterface;
use App\Components\Football\Persitence\DTOs\PlayerDto;

readonly class ApiRequestFacade implements ApiRequestFacadeInterface
{
    public function __construct(private ApiRequesterInterface $apiRequester)
    {
    }

    public function getTeam(int $id): array
    {
        return $this->apiRequester->getTeam($id);
    }

    public function getLeagueTeams(string $code): array
    {
        return $this->apiRequester->getLeagueTeams($code);
    }

    public function getLeagues(): array
    {
        return $this->apiRequester->getLeagues();
    }

    public function getPlayer(string $playerId): ?PlayerDto
    {
        return $this->apiRequester->getPlayer($playerId);
    }
}
