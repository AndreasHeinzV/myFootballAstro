<?php

declare(strict_types=1);

namespace App\Components\Api\Business\Model;

use App\Components\Football\Persitence\DTOs\PlayerDto;
use App\Components\Football\Persitence\Mapper\LeaguesMapperInterface;
use App\Components\Football\Persitence\Mapper\LeagueTeamsMapperInterface;
use App\Components\Football\Persitence\Mapper\PlayerMapperInterface;
use App\Components\Football\Persitence\Mapper\TeamMapperInterface;
use App\Service\CacheService;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ApiRequester implements ApiRequesterInterface
{
    private string $apiKey;
    private CacheService $cacheService;

    public function __construct(
        string $apiKey,
        private HttpClientInterface $httpClient,
        private LeaguesMapperInterface $leaguesMapper,
        private LeagueTeamsMapperInterface $leagueTeamsMapper,
        private TeamMapperInterface $teamMapper,
        private PlayerMapperInterface $playerMapper,
        CacheService $cacheService,
    ) {
        $this->apiKey = $apiKey;
        $this->cacheService = $cacheService;
    }

    public function apiRequest(string $url): array
    {
        $cacheKey = md5($url);

        return $this->cacheService->getOrSet($cacheKey, function () use ($url) {
            $response = $this->httpClient->request(
                'GET',
                $url,
                [
                    'headers' => [
                        'X-Auth-Token' => $this->apiKey,
                    ],
                ]
            );

            $statusCode = $response->getStatusCode();
            if (200 !== $statusCode) {
                throw new \RuntimeException("API request failed with status: $statusCode");
            }

            return $response->toArray();
        });
    }

    public function getLeagues(): array
    {
        $uri = 'https://api.football-data.org/v4/competitions/';
        $matches = $this->apiRequest($uri);
        $leaguesArray = [];

        foreach ($matches['competitions'] as $competition) {
            $leagueArray = [];
            $leagueArray['id'] = $competition['id'];
            $leagueArray['leagueId'] = (string) $competition['code'];
            $leagueArray['name'] = $competition['name'];
            $leagueDTO = $this->leaguesMapper->createLeaguesDto($leagueArray);

            $leaguesArray[] = $leagueDTO;
        }

        return $leaguesArray;
    }

    public function getTeam(int $id): array
    {
        $uri = 'https://api.football-data.org/v4/teams/'.$id;
        $team = $this->apiRequest($uri);
        $playersArray = [];

        $playersArray['teamName'] = $team['name'];
        $playersArray['teamID'] = $team['id'];
        $playersArray['crest'] = $team['crest'];

        foreach ($team['squad'] as $player) {
            $playerArray['playerID'] = $player['id'];
            $playerArray['link'] = '?page=player&id='.$player['id'];
            $playerArray['name'] = $player['name'];
            $playersArray['squad'][] = $this->teamMapper->createTeamDto($playerArray);
        }

        return $playersArray;
    }

    public function getLeagueTeams(string $code): array
    {
        $teams = [];
        $uri = 'https://api.football-data.org/v4/competitions/'.$code.'/standings';
        $standings = $this->apiRequest($uri);

        $teamID = $standings['standings'][0]['table'];
        foreach ($teamID as $table) {
            $team = [];
            $team['position'] = $table['position'];
            $team['name'] = $table['team']['name'];
            $team['teamId'] = $table['team']['id'];
            $team['playedGames'] = $table['playedGames'];
            $team['won'] = $table['won'];
            $team['draw'] = $table['draw'];
            $team['lost'] = $table['lost'];
            $team['points'] = $table['points'];
            $team['goalsFor'] = $table['goalsFor'];
            $team['goalsAgainst'] = $table['goalsAgainst'];
            $team['goalDifference'] = $table['goalDifference'];
            $teams[] = $this->leagueTeamsMapper->createLeagueTeamsDTO($team);
        }

        return $teams;
    }

    public function getPlayer(string $playerId): ?PlayerDto
    {
        $uri = 'https://api.football-data.org/v4/persons/'.$playerId;

        if (empty($this->apiRequest($uri))) {
            return null;
        }

        return $this->playerMapper->createTeamDTO($this->apiRequest($uri));
    }
}
