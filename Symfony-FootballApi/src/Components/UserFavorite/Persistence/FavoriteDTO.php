<?php

declare(strict_types=1);

namespace App\Components\UserFavorite\Persistence;

readonly class FavoriteDTO
{
    public function __construct(
        public int $teamID,
        public string $teamName,
        public string $crest,
        public int $position,
    ) {
    }
}