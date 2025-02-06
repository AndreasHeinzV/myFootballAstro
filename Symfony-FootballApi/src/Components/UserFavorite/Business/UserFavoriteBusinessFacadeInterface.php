<?php

namespace App\Components\UserFavorite\Business;

use App\Entity\User;

interface UserFavoriteBusinessFacadeInterface
{
    public function getFavoriteStatus(User $user, int $teamId): bool;

    public function getUserFavorites(User $user): array;

    public function addFavorite(User $user, int $teamId);

    public function removeFavorite(User $user, int $teamId);

    public function moveUpFavorite(User $user, int $teamId): void;

    public function moveDownFavorite(User $user, int $teamId): void;
}
