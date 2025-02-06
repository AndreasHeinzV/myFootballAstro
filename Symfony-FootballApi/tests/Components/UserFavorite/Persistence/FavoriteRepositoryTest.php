<?php

declare(strict_types=1);

namespace App\Tests\Components\UserFavorite\Persistence;

use App\Components\UserFavorite\Persistence\FavoriteDTO;
use App\Components\UserFavorite\Persistence\FavoriteRepository;
use App\Entity\Favorite;
use App\Tests\BaseTestCase;


class FavoriteRepositoryTest extends BaseTestCase
{
    private FavoriteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->repository = $container->get(FavoriteRepository::class);
    }

    public function testGetAllFavoritesFromFirstUser(): void
    {
        $favorites = $this->repository->getUserFavorites(1);
        self::assertNotEmpty($favorites);
        self::assertInstanceOf(FavoriteDTO::class, $favorites[0]);
        self::assertCount(3, $favorites);
    }

    public function testGetUserFavoriteFromUserWithoutFavorites(): void
    {
        $favorites = $this->repository->getUserFavorites(2);
        self::assertEmpty($favorites);
    }

    public function testGetUserFavoritesWithWrongUserId(): void
    {
        $favorites = $this->repository->getUserFavorites(4);
        self::assertEmpty($favorites);
    }

    public function testGetUserFavoriteWithTeamIdOne(): void
    {
        $favorite = $this->repository->getUserFavoriteByTeamId(1, 1);
        self::assertNotEmpty($favorite);
        self::assertInstanceOf(Favorite::class, $favorite);
        self::assertSame(1, $favorite->getTeamId());
        self::assertSame('team1', $favorite->getTeamName());
    }

    public function testGetUserFavoriteWithWrongTeamId(): void
    {
        $favorite = $this->repository->getUserFavoriteByTeamId(2, 6);
        self::assertNull($favorite);
    }

    public function testGetUserFavoritesFirstFavoritePosition(): void
    {
        $favoritePosition = $this->repository->getUserFavoritesFirstPosition(1);

        self::assertSame(1, $favoritePosition);
    }

    public function testGetUserFavoritesLastFavoritePosition(): void
    {
        $favoritePosition = $this->repository->getUserFavoritesLastPosition(1);

        self::assertSame(3, $favoritePosition);
    }

    public function testGetUserFavoriteFirstPositionForEmptyUsers(): void
    {
        $favoritePosition = $this->repository->getUserFavoritesFirstPosition(2);

        self::assertFalse($favoritePosition);
    }

    public function testGetUserFavoriteLastPositionForEmptyUsers(): void
    {
        $favoritePosition = $this->repository->getUserFavoritesLastPosition(2);
        self::assertFalse($favoritePosition);
    }

    public function testGetUserFavoriteEntityWithPosition(): void
    {
        $favoriteEntity = $this->repository->getUserFavoriteEntityByPosition(1, 2);
        self::assertInstanceOf(Favorite::class, $favoriteEntity);
        self::assertSame(2, $favoriteEntity->getFavoritePosition());
        self::assertSame('team2', $favoriteEntity->getTeamName());
    }

    public function testGetUserFavoriteEntityWithWrongPosition(): void
    {
        $favoriteEntity = $this->repository->getUserFavoriteEntityByPosition(2, 6);
        self::assertNull($favoriteEntity);
    }
}
