<?php

namespace App\Components\User\Persistence;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findUserByMail(string $email): ?User
    {
        $user = $this->findOneBy(['email' => $email]);

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    public function getUserIdentifierByToken(string $token): ?string
    {
        $user = $this->findOneBy(['token' => $token]);
        if ($user instanceof User) {
            return $user->getUserIdentifier();
        }
        return $user->getUserIdentifier();
    }

    public function getUserByToken(string $token): ?User
    {
       return $this->findOneBy(['token' => $token]);

    }
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
