<?php

declare(strict_types=1);

namespace App\Tests\Components\Football\Communication;

use App\Components\Api\Business\Model\ApiRequesterInterface;
use App\Components\Football\Persitence\Mapper\LeaguesMapper;
use App\Components\Football\Persitence\Mapper\LeagueTeamsMapper;
use App\Components\Football\Persitence\Mapper\PlayerMapper;
use App\Components\Football\Persitence\Mapper\TeamMapper;
use App\Tests\Fixtures\ApiRequest\ApiRequestFaker;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TeamDetailsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $container = static::getContainer();
        $apiRequesterFaker = new ApiRequestFaker(
            $container->get(PlayerMapper::class),
            $container->get(TeamMapper::class),
            $container->get(LeaguesMapper::class),
            $container->get(LeagueTeamsMapper::class),
        );
        $container->set(ApiRequesterInterface::class, $apiRequesterFaker);
    }

    public function testTeamDetails(): void
    {
        self::bootKernel();
        $url = 'http://127.0.0.1:8000/team/SV%20Werder%20Bremen/12';
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();
        $data = json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR);
        $players = $data['players'];
        self::assertResponseIsSuccessful($responseContent);

        self::assertResponseHeaderSame('Content-Type', 'application/json');
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('success', $data['status']);
        self::assertSame(9438, $players[0]['playerId']);
    }

}