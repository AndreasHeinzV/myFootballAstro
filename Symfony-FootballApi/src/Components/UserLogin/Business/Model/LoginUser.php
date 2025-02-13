<?php

declare(strict_types=1);

namespace App\Components\UserLogin\Business\Model;

use App\Components\User\Business\UserBusinessFacade;
use App\Components\User\Business\UserBusinessFacadeInterface;
use App\Components\User\Persistence\UserEntityManager;
use App\Entity\User;

readonly class LoginUser
{

    public function __construct(private UserBusinessFacade $userBusiness){

    }

    public function updateLoginCredentials(string $email, string $token): void {

       $user=  $this->userBusiness->getUserEntityByMail($email);
        if ($user instanceof User) {
            $user->setToken($token);
            $user->setTokenCreatedAt(new \DateTime());
            $this->userBusiness->updateUserEntity($user);
        }
    }
}