<?php

declare(strict_types=1);

namespace App\Components\User\Persistence;

readonly class UserDto
{
    public function __construct(
        public ?int $userId,
        public ?string $firstName,
        public ?string $lastName,
        public string $email,
        public string $password,
    ) {
    }
}
