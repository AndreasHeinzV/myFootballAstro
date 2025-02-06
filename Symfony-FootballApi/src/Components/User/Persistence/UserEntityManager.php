<?php

declare(strict_types=1);

namespace App\Components\User\Persistence;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

readonly class UserEntityManager
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function saveUser(UserDto $userDto): void
    {
        $user = new User();
        $user->setFirstName($userDto->firstName);
        $user->setLastName($userDto->lastName);
        $user->setEmail($userDto->email);
        $user->setPassword($userDto->password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
