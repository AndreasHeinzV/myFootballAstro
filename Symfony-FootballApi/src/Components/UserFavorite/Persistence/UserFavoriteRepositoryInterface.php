<?php

namespace App\Components\UserFavorite\Persistence;

use App\Components\User\Persistence\UserDto;
use App\Entity\Favorite;
use App\Entity\User;

interface UserFavoriteRepositoryInterface
{
    public function getUserFavorites(int $userId): array;

    public function getUserFavoriteByTeamId(int $userId, int $teamId): ?Favorite;

    public function getUserFavoritesFirstPosition(int $userId): int|false;

    public function getUserFavoritesLastPosition(int $userId): int|false;

    public function getFavoritePositionAboveCurrentPosition(int $userId, int $position): int|false;

    public function getUserFavoriteEntityByPosition(int $userId, int $position): ?Favorite;

    public function getFavoritePositionBelowCurrentPosition(int $userId, int $position): int|false;
}
