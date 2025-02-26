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

class HomePageControllerTest extends WebTestCase
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

    public function testGetLeagues(): void
    {
        $crawler = $this->client->request('GET', '/leagues');

        self::assertResponseIsSuccessful();
        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $leaguesArray = $data["data"];
        $bundesliga = $leaguesArray[6];
        $this->assertSame($bundesliga['id'] ,2002);
        $this->assertNotEmpty($data);

        //$this->assertGreaterThan(0, $linkCrawler->count(), 'The link containing "Bundesliga" should exist.');
       // $hrefCheck = $crawler->filter('a[href$="BL1"]');
      //  $this->assertGreaterThan(0, $hrefCheck->count(), 'The href ending with "BL1" should exist.');
    }

}
