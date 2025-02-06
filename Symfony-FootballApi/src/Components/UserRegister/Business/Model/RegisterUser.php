<?php

declare(strict_types=1);

namespace App\Components\UserRegister\Business\Model;

use App\Components\User\Persistence\UserDto;
use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class RegisterUser
{

    public function __construct(
        private UserEntityManager $entityManager,
        private UserRepository $userRepository
    ) {
    }

    public function saveUser(UserDto $dto): void
    {
        $this->entityManager->saveUser($dto);
    }

    public function checkExistingUser(UserDto $dto): bool
    {
        $user = $this->userRepository->findUserByMail($dto->email);
        return $user instanceof User;
    }

}