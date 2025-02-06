<?php

declare(strict_types=1);

namespace App\Components\UserFavorite\Persistence;

use App\Entity\Favorite;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class UserFavoriteEntityManager implements UserFavoriteEntityManagerInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function saveUserFavorite(User $user, FavoriteDTO $favoriteDTO): void
    {
        $favorite = new Favorite();
        $favorite->setFavoritePosition($favoriteDTO->position);
        $favorite->setTeamCrest($favoriteDTO->crest);
        $favorite->setTeamName($favoriteDTO->teamName);
        $favorite->setTeamId($favoriteDTO->teamID);
        $favorite->setUser($user);

        $this->entityManager->persist($favorite);
        $this->entityManager->flush();
    }

    public function updateUserFavoritePosition(
        Favorite $favoriteEntity,
        Favorite $favoriteEntityChange,
    ): void {
        $positionT = $favoriteEntity->getFavoritePosition();
        $favoriteEntity->setFavoritePosition($favoriteEntityChange->getFavoritePosition());
        $favoriteEntityChange->setFavoritePosition($positionT);
        $this->entityManager->persist($favoriteEntity);
        $this->entityManager->persist($favoriteEntityChange);
        $this->entityManager->flush();
    }

    public function deleteUserFavorite(User $user, int $teamId): void
    {
        $favoriteEntity = $this->entityManager->getRepository(Favorite::class)->findOneBy(
            ['user' => $user, 'teamId' => $teamId]
        );

        if (null !== $favoriteEntity) {
            $this->entityManager->remove($favoriteEntity);
            $this->entityManager->flush();
        }
    }

    public function refresh(object $entity): void
    {
        $this->entityManager->refresh($entity);
    }
}
