<?php

declare(strict_types=1);

namespace App\Components\User\Business;

use App\Components\User\Persistence\UserEntityManager;
use App\Components\User\Persistence\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserBusinessFacade implements UserBusinessFacadeInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getUserEntity(UserInterface $user): ?User
    {
        return $this->userRepository->findUserByMail($user->getEmail());
    }

    public function getUserEntityByMail(string $email): ?User
    {
        return $this->userRepository->findUserByMail($email);
    }

    public function getUserEntityByToken(string $token): ?User
    {
        return $this->userRepository->getUserByToken($token);
    }

    public function updateUserEntity(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function resetUserSession(User $user): void
    {
        $user->setTokenCreatedAt(null);
        $user->setToken(null);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}