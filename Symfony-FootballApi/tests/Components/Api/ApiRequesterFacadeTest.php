<?php

declare(strict_types=1);

namespace App\Tests\Components\Api;

use App\Components\Api\ApiRequestFacade;
use App\Components\Football\Persitence\DTOs\PlayerDto;
use App\Components\Football\Persitence\Mapper\LeaguesMapper;
use App\Components\Football\Persitence\Mapper\LeagueTeamsMapper;
use App\Components\Football\Persitence\Mapper\PlayerMapper;
use App\Components\Football\Persitence\Mapper\TeamMapper;
use App\Tests\Fixtures\ApiRequest\ApiRequestFaker;
use PHPUnit\Framework\TestCase;

class ApiRequesterFacadeTest extends TestCase
{
    private ApiRequestFacade $apiRequestFacade;
    private ApiRequestFaker $apiRequesterFaker;

    protected function setUp(): void
    {
        $playerMapper = new PlayerMapper();
        $teamMapper = new TeamMapper();
        $leaguesMapper = new LeaguesMapper();
        $leagueTeamMapper = new LeagueTeamsMapper();
        $this->apiRequesterFaker = new ApiRequestFaker($playerMapper, $teamMapper, $leaguesMapper, $leagueTeamMapper);
        $this->apiRequestFacade = new ApiRequestFacade($this->apiRequesterFaker);
        parent::setUp();
    }

    public function testGetTeam(): void
    {
        $teamId = 3984;
        $team = $this->apiRequestFacade->getTeam($teamId);

        self::assertNotEmpty($team);
        self::assertArrayHasKey('teamName', $team);
        self::assertEquals('Fortaleza EC', $team['teamName']);
    }

    public function testGetLeagueTeams(): void
    {
        $leagueCode = 'BSA';
        $teams = $this->apiRequestFacade->getLeagueTeams($leagueCode);

        self::assertCount(20, $teams);
        self::assertNotSame('', $teams[0]->name);
    }

    public function testGetLeagues(): void
    {
        $leagues = $this->apiRequestFacade->getLeagues();

        $bool = false;
        foreach ($leagues as $league) {
            if ('Bundesliga' === $league->name) {
                $bool = true;
            }
        }
        self::assertTrue($bool);
    }

    public function testGetPlayer(): void
    {
        $playerId = '348';
        $player = $this->apiRequestFacade->getPlayer($playerId);
        self::assertInstanceOf(PlayerDto::class, $player);
        self::assertNotEmpty($player->name);
    }

    public function testApiRequestGetNoPlayer(): void
    {
        $playerDTO = $this->apiRequestFacade->getPlayer('129943458679');
        self::assertEmpty($playerDTO);
    }
}
