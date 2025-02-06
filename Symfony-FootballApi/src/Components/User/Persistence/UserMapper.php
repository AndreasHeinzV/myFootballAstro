<?php

declare(strict_types=1);

namespace App\Components\User\Persistence;

class UserMapper
{
    public function createUserDto(array $userData): UserDto
    {
        return new UserDto(
            $userData['userId'] ?? null,
            $userData['firstName'] ?? null,
            $userData['lastName'] ?? null,
            $userData['email'],
            $userData['password']
        );
    }
}