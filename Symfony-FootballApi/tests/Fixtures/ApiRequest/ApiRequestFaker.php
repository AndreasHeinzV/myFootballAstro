<?php

declare(strict_types=1);

namespace App\Tests\Fixtures\ApiRequest;

use App\Components\Api\Business\Model\ApiRequesterInterface;
use App\Components\Football\Persitence\DTOs\PlayerDto;
use App\Components\Football\Persitence\Mapper\LeaguesMapperInterface;
use App\Components\Football\Persitence\Mapper\LeagueTeamsMapperInterface;
use App\Components\Football\Persitence\Mapper\PlayerMapperInterface;
use App\Components\Football\Persitence\Mapper\TeamMapperInterface;

readonly class ApiRequestFaker implements ApiRequesterInterface
{
    public function __construct(
        private PlayerMapperInterface $playerMapper,
        private TeamMapperInterface $teamMapper,
        private LeaguesMapperInterface $leaguesMapper,
        private LeagueTeamsMapperInterface $leagueTeamsMapper,
    ) {
    }

    public function apiRequest(string $url): array
    {
        $filename = str_replace(['https://api.football-data.org/v4/', '/'], [''], $url);
        $path = __DIR__.'/cache/'.$filename.'.json';

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true);
    }

    public function getTeam(int $id): array
    {
        $uri = 'https://api.football-data.org/v4/teams/'.$id;
        $team = $this->apiRequest($uri);
        $playersArray = [];

        if (empty($team)) {
            return [];
        }

        $playersArray['teamName'] = $team['name'];
        $playersArray['teamID'] = $team['id'];
        $playersArray['crest'] = $team['crest'];

        foreach ($team['squad'] as $player) {
            $playerArray['playerID'] = $player['id'];
            $playerArray['link'] = '/index.php?page=player&id='.$player['id'];
            $playerArray['name'] = $player['name'];
            $playersArray['squad'][] = $this->teamMapper->createTeamDTO($playerArray);
        }

        return $playersArray;
    }

    public function getLeagueTeams(string $code): array
    {
        $teams = [];
        $uri = 'https://api.football-data.org/v4/competitions/'.$code.'/standings';
        $standings = $this->apiRequest($uri);

        if (empty($standings)) {
            return [];
        }

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

    public function getPlayer(string $playerId): ?PlayerDto
    {
        $uri = 'https://api.football-data.org/v4/persons/'.$playerId;
        if (empty($this->apiRequest($uri))) {
            return null;
        }
        return $this->playerMapper->createTeamDTO($this->apiRequest($uri));
    }
}
