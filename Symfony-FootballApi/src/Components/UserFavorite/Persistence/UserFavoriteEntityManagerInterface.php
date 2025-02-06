<?php

namespace App\Components\UserFavorite\Persistence;

use App\Components\User\Persistence\UserDto;
use App\Entity\Favorite;
use App\Entity\User;

interface UserFavoriteEntityManagerInterface
{
    public function saveUserFavorite(User $user, FavoriteDTO $favoriteDTO): void;

    public function updateUserFavoritePosition(
        Favorite $favoriteEntity,
        Favorite $favoriteEntityChange,
    ): void;

    public function deleteUserFavorite(User $user, int $teamId): void;
}
