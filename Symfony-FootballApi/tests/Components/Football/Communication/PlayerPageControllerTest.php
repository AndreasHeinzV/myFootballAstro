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
        $url = '/player/Andrea%20Natali/274021';
        $this->client->request('GET', $url);

        $response = $this->client->getResponse();
        $responseContent = $response->getContent();
        $data = json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR);
        self::assertResponseIsSuccessful($responseContent);

       self::assertResponseHeaderSame('Content-Type', 'application/json');
       self::assertSame(200, $response->getStatusCode());
       self::assertSame('success', $data['status']);
       self::assertSame('Andrea Natali', $data['data']['name']);
    }
}
