<?php

declare(strict_types=1);

namespace App\Tests\Components\UserFavorite\Business;

use App\Components\Api\Business\Model\ApiRequesterInterface;
use App\Components\Football\Persitence\Mapper\LeaguesMapper;
use App\Components\Football\Persitence\Mapper\LeagueTeamsMapper;
use App\Components\Football\Persitence\Mapper\PlayerMapper;
use App\Components\Football\Persitence\Mapper\TeamMapper;
use App\Components\User\Persistence\UserRepository;
use App\Components\UserFavorite\Business\Model\FavoriteCalc;
use App\Components\UserFavorite\Persistence\FavoriteDTO;
use App\Components\UserFavorite\Persistence\FavoriteRepository;
use App\Tests\BaseTestCase;
use App\Tests\Fixtures\ApiRequest\ApiRequestFaker;

class FavoriteCalcTest extends BaseTestCase
{
    private FavoriteCalc $favoriteCalc;
    private UserRepository $userRepository;

    private FavoriteRepository $favoriteRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $apiRequesterFaker = new ApiRequestFaker(
            $container->get(PlayerMapper::class),
            $container->get(TeamMapper::class),
            $container->get(LeaguesMapper::class),
            $container->get(LeagueTeamsMapper::class),
        );
        $container->set(ApiRequesterInterface::class, $apiRequesterFaker);

        $this->favoriteCalc = $container->get(FavoriteCalc::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->favoriteRepository = $container->get(FavoriteRepository::class);
    }

    public function testAddUserFavoriteTeam(): void
    {
        $favoritesBefore = $this->favoriteRepository->getUserFavorites(1);
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $this->favoriteCalc->handleAdd($userEntity, 3984);
        $favoritesAfter = $this->favoriteRepository->getUserFavorites(1);

        self::assertNotEmpty($favoritesBefore);
        self::assertCount(3, $favoritesBefore);
        self::assertNotEmpty($favoritesAfter);
        self::assertCount(4, $favoritesAfter);
        self::assertInstanceOf(FavoriteDTO::class, $favoritesAfter[3]);
        self::assertSame(3984, $favoritesAfter[3]->teamID);
    }

    public function testRemoveUserFavoriteTeam(): void
    {
        $favoritesBefore = $this->favoriteRepository->getUserFavorites(1);
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $this->favoriteCalc->handleRemove($userEntity, 3);
        $favoritesAfter = $this->favoriteRepository->getUserFavorites(1);

        self::assertNotEmpty($favoritesBefore);
        self::assertCount(3, $favoritesBefore);
        self::assertNotEmpty($favoritesAfter);
        self::assertCount(2, $favoritesAfter);
    }

    public function testGetFavoriteStatus(): void
    {
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $status = $this->favoriteCalc->getFavStatus($userEntity, 1);
        self::assertTrue($status);
    }

    public function testGetFavoriteStatusNotExistingFavorite(): void
    {
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $status = $this->favoriteCalc->getFavStatus($userEntity, 4);
        self::assertFalse($status);
    }

    public function testGetFavoriteStatusEmptyFavorites(): void
    {
        $userEntity = $this->userRepository->findUserByMail('user2@example.com');
        $status = $this->favoriteCalc->getFavStatus($userEntity, 1);
        self::assertFalse($status);
    }

    public function testMoveUserFavoriteTeamDown(): void
    {
        $favoritesBefore = $this->favoriteRepository->getUserFavorites(1);
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $this->favoriteCalc->userFavoriteDown($userEntity, 1);
        $favoritesAfter = $this->favoriteRepository->getUserFavorites(1);
        self::assertNotEmpty($favoritesBefore);
        self::assertSame(1, $favoritesBefore[0]->teamID);
        self::assertNotSame($favoritesAfter[0]->teamID, $favoritesBefore[0]->teamID);
        self::assertSame(2, $favoritesAfter[0]->teamID);
    }

    public function testMoveUserFavoriteTeamDownFailed(): void
    {
        $favoritesBefore = $this->favoriteRepository->getUserFavorites(1);
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $this->favoriteCalc->userFavoriteDown($userEntity, 3);
        $favoritesAfter = $this->favoriteRepository->getUserFavorites(1);

        self::assertNotEmpty($favoritesBefore);
        self::assertSame(3, $favoritesBefore[2]->teamID);
        self::assertSame($favoritesAfter[0]->teamID, $favoritesBefore[0]->teamID);
        self::assertSame(3, $favoritesAfter[2]->teamID);
    }

    public function testMoveUserFavoriteTeamUp(): void
    {
        $favoritesBefore = $this->favoriteRepository->getUserFavorites(1);
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $this->favoriteCalc->userFavoriteUp($userEntity, 2);
        $favoritesAfter = $this->favoriteRepository->getUserFavorites(1);

        self::assertNotEmpty($favoritesBefore);
        self::assertSame(1, $favoritesBefore[0]->teamID);
        self::assertNotSame($favoritesAfter[0]->teamID, $favoritesBefore[0]->teamID);
        self::assertSame(2, $favoritesAfter[0]->teamID);
    }

    public function testMoveUserFavoriteTeamUpFailed(): void
    {
        $favoritesBefore = $this->favoriteRepository->getUserFavorites(1);
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $this->favoriteCalc->userFavoriteUp($userEntity, 1);
        $favoritesAfter = $this->favoriteRepository->getUserFavorites(1);

        self::assertNotEmpty($favoritesBefore);
        self::assertSame(1, $favoritesBefore[0]->teamID);
        self::assertSame($favoritesAfter[0]->teamID, $favoritesBefore[0]->teamID);
        self::assertSame(1, $favoritesAfter[0]->teamID);
    }
}
