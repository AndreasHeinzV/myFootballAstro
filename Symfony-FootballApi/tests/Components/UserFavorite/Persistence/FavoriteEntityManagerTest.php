<?php

declare(strict_types=1);

namespace App\Tests\Components\UserFavorite\Persistence;

use App\Components\User\Persistence\UserRepository;
use App\Components\UserFavorite\Persistence\FavoriteDTO;
use App\Components\UserFavorite\Persistence\FavoriteRepository;
use App\Components\UserFavorite\Persistence\UserFavoriteEntityManager;
use App\Tests\BaseTestCase;

class FavoriteEntityManagerTest extends BaseTestCase
{
    private FavoriteRepository $repository;

    private UserRepository $userRepository;
    private UserFavoriteEntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->repository = $container->get(FavoriteRepository::class);
        $this->userRepository = $container->get(UserRepository::class);
        $this->entityManager = $container->get(UserFavoriteEntityManager::class);
    }

    public function testSaveUserFavorite(): void
    {
        $favoriteDto = new FavoriteDTO(42, 'teamTest', 'testCrest', 5);
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $favoritesBefore = $this->repository->getUserFavorites(1);

        $this->entityManager->saveUserFavorite($userEntity, $favoriteDto);
        $favoritesAfter = $this->repository->getUserFavorites(1);
        self::assertCount(3, $favoritesBefore);
        self::assertCount(4, $favoritesAfter);
        self::assertSame('teamTest', $favoritesAfter[3]->teamName);
    }

    public function testDeleteUserFavorite(): void
    {
        $userEntity = $this->userRepository->findUserByMail('user1@example.com');
        $favoritesBefore = $this->repository->getUserFavorites(1);

        $this->entityManager->deleteUserFavorite($userEntity, 1);
        $favoritesAfter = $this->repository->getUserFavorites(1);
        self::assertCount(3, $favoritesBefore);
        self::assertCount(2, $favoritesAfter);
        self::assertNotSame($favoritesBefore[0], $favoritesAfter[0]);
    }

    public function testUpdateUserFavoritePosition(): void
    {
        $favoriteEntityOne = $this->repository->getUserFavoriteByTeamId(1, 1);
        $position = $favoriteEntityOne->getFavoritePosition();
        $favoriteEntityTwo = $this->repository->getUserFavoriteByTeamId(1, 2);
        $this->entityManager->updateUserFavoritePosition($favoriteEntityOne, $favoriteEntityTwo);
        $favoriteEntityOneAfter = $this->repository->getUserFavoriteByTeamId(1, 1);
        $this->entityManager->refresh($favoriteEntityOne);

        self::assertNotSame($position, $favoriteEntityOneAfter->getFavoritePosition());
    }

}
