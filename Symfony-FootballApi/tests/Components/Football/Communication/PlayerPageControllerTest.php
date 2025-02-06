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

class PlayerPageControllerTest extends WebTestCase
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

    public function testGetPlayer(): void
    {
        self::bootKernel();

        $this->client->request('GET', '/team/Eintracht%20Frankfurt/player/Kaua?page=player&id=189482');

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();
        self::assertResponseIsSuccessful($responseContent);
        self::assertStringContainsString('Kaua', $responseContent);
    }
}
