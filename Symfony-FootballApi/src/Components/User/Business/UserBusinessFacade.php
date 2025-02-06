<?php

declare(strict_types=1);

namespace App\Components\User\Business;

use App\Components\User\Persistence\UserRepository;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserBusinessFacade implements UserBusinessFacadeInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function getUserEntity(UserInterface $user): ?User
    {
        return $this->userRepository->findUserByMail($user->getEmail());
    }
}