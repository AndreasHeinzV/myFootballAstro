<?php

declare(strict_types=1);

namespace App\Components\UserLogin\Business\Model;

use App\Components\User\Business\UserBusinessFacade;

class LogoutUser
{

    public function __construct(public UserBusinessFacade $userBusiness)
    {
    }

    public function logoutUser(string $token): bool
    {


        $user = $this->userBusiness->getUserEntityByToken($token);
        if (null === $user) {
            return false;
        }

        $this->userBusiness->resetUserSession($user);


        return true;
    }
}