<?php

namespace App\Components\UserFavorite\Persistence;

use App\Entity\Favorite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository implements UserFavoriteRepositoryInterface
{
    public function __construct(ManagerRegistry $registry, private readonly FavoriteMapper $favoriteMapper)
    {
        parent::__construct($registry, Favorite::class);
    }

    //    /**
    //     * @return FavoriteCalc[] Returns an array of FavoriteCalc objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?FavoriteCalc
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function getUserFavorites(int $userId): array
    {
        // Perform a query to find favorites by the user
        $query = $this->createQueryBuilder('f')
            ->innerJoin('f.user', 'u')
            ->where('u.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('f.favoritePosition', 'ASC')
            ->getQuery();

        $userFavorites = $query->getResult();

        if (empty($userFavorites)) {
            return [];
        }

        $favoriteDTOArray = [];
        foreach ($userFavorites as $favorite) {
            $returnValue = [
                'teamName' => $favorite->getTeamName(),
                'teamID' => $favorite->getTeamId(),
                'crest' => $favorite->getTeamCrest(),
                'favoritePosition' => $favorite->getFavoritePosition(),
            ];
            $favoriteDTOArray[] = $this->favoriteMapper->createFavoriteDTO($returnValue);
        }

        return $favoriteDTOArray;
    }

    public function getUserFavoriteByTeamId(int $userId, int $teamId): ?Favorite
    {
        $user = $this->getEntityManager()->getReference(User::class, $userId);

        return $this->findOneBy([
            'user' => $user,
            'teamId' => $teamId,
        ]);
    }

    public function getUserFavoritesFirstPosition(int $userId): int|false
    {
        $user = $this->getEntityManager()->getReference(User::class, $userId);

        $favorites = $this->findBy(
            ['user' => $user],
            ['favoritePosition' => 'ASC']
        );
        if (!empty($favorites)) {
            return $favorites[0]->getFavoritePosition();
        }

        return false;
    }

    public function getUserFavoritesLastPosition(int $userId): int|false
    {
        $user = $this->getEntityManager()->getReference(User::class, $userId);

        $favorites = $this->findBy(
            ['user' => $user],
            ['favoritePosition' => 'DESC']
        );
        if (!empty($favorites)) {
            return $favorites[0]->getFavoritePosition();
        }

        return false;
    }

    public function getFavoritePositionAboveCurrentPosition(int $userId, int $position): int|false
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('f')
            ->from(Favorite::class, 'f')
            ->innerJoin('f.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('f.favoritePosition < :favoritePosition')
            ->setParameter('userId', $userId)
            ->setParameter('favoritePosition', $position)
            ->orderBy('f.favoritePosition', 'DESC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result instanceof Favorite) {
            return $result->getFavoritePosition();
        }

        return false;
    }

    public function getFavoritePositionBelowCurrentPosition(int $userId, int $position): int|false
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('f')
            ->from(Favorite::class, 'f')
            ->innerJoin('f.user', 'u')
            ->where('u.id = :userId')
            ->andWhere('f.favoritePosition > :favoritePosition')
            ->setParameter('userId', $userId)
            ->setParameter('favoritePosition', $position)
            ->orderBy('f.favoritePosition', 'ASC')
            ->setMaxResults(1);

        $result = $qb->getQuery()->getOneOrNullResult();

        if ($result instanceof Favorite) {
            return $result->getFavoritePosition();
        }

        return false;
    }

    public function getUserFavoriteEntityByPosition(int $userId, int $position): ?Favorite
    {
        $user = $this->getEntityManager()->getReference(User::class, $userId);

        return $this->findOneBy([
            'user' => $user,
            'favoritePosition' => $position,
        ]);
    }
}
