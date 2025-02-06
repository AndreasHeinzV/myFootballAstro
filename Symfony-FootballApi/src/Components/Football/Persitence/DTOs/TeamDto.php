<?php

declare(strict_types=1);

namespace App\Components\Football\Persitence\DTOs;

readonly class TeamDto
{
    public function __construct(
        public int $playerID,
        public string $link,
        public string $name,
    ) {
    }
}