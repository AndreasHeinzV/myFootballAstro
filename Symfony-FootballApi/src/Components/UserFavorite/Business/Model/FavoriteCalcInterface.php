<?php

namespace App\Components\UserFavorite\Business\Model;

use App\Entity\User;

interface FavoriteCalcInterface
{

    public function handleRemove(User $user, int $teamId): void;

    public function handleAdd(User $user, int $teamId): void;

    public function getFavStatus(User $user, int $teamId): bool;

    public function userFavoriteUp(User $user, int $teamId): void;

    public function userFavoriteDown(User $user, int $teamId): void;
}
