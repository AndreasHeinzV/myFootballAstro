<?php

declare(strict_types=1);

namespace App\Components\UserFavorite\Business;

use App\Components\UserFavorite\Business\Model\FavoriteCalcInterface;
use App\Components\UserFavorite\Persistence\UserFavoriteRepositoryInterface;
use App\Entity\User;

readonly class UserFavoriteBusinessFacade implements UserFavoriteBusinessFacadeInterface
{
    public function __construct(
        private FavoriteCalcInterface $favorite,
        private UserFavoriteRepositoryInterface $userFavoriteRepository,
        private FavoriteCalcInterface $favoriteCalc,
    ) {
    }

    public function getFavoriteStatus(User $user, int $teamId): bool
    {
        return $this->favorite->getFavStatus($user, $teamId);
    }

    public function getUserFavorites(User $user): array
    {
        return $this->userFavoriteRepository->getUserFavorites($user->getId());
    }

    public function addFavorite(User $user, int $teamId): void
    {
        $this->favoriteCalc->handleAdd($user, $teamId);
    }

    public function removeFavorite(User $user, int $teamId): void
    {
        $this->favoriteCalc->handleRemove($user, $teamId);
    }

    public function moveUpFavorite(User $user, int $teamId): void
    {
        $this->favoriteCalc->userFavoriteUp($user, $teamId);
    }

    public function moveDownFavorite(User $user, int $teamId): void
    {
        $this->favoriteCalc->userFavoriteDown($user, $teamId);
    }
}
