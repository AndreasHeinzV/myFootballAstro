<?php

declare(strict_types=1);

namespace App\Components\UserFavorite\Business\Model;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use App\Components\UserFavorite\Persistence\FavoriteMapper;
use App\Components\UserFavorite\Persistence\UserFavoriteEntityManagerInterface;
use App\Components\UserFavorite\Persistence\UserFavoriteRepositoryInterface;
use App\Entity\Favorite;
use App\Entity\User;

use function PHPUnit\Framework\throwException;

readonly class FavoriteCalc implements FavoriteCalcInterface
{
    public function __construct(
        private FootballBusinessFacadeInterface $footballBusinessFacade,
        private UserFavoriteEntityManagerInterface $userFavoriteEntityManager,
        private UserFavoriteRepositoryInterface $userFavoriteRepository,
        private FavoriteMapper $favoriteMapper,
    ) {
    }

    public function handleRemove(User $user, int $teamId): void
    {
        $this->userFavoriteEntityManager->deleteUserFavorite($user, $teamId);
    }

    public function handleAdd(User $user, int $teamId): void
    {
        $team = $this->footballBusinessFacade->getTeam($teamId);
        $position = $this->calculatePosition($user);
        if (!empty($team) && !$this->getFavStatus($user, $teamId)) {
            $team['favoritePosition'] = $position;
            $this->userFavoriteEntityManager->saveUserFavorite(
                $user,
                $this->favoriteMapper->createFavoriteDTO($team)
            );
        }
    }

    private function calculatePosition(User $user): int
    {
        $lastPosition = $this->userFavoriteRepository->getUserFavoritesLastPosition($user->getId());
        if (false === $lastPosition) {
            return 1;
        }

        return $lastPosition + 1;
    }

    public function getFavStatus(User $user, int $teamId): bool
    {
        $favoriteEntity = $this->userFavoriteRepository->getUserFavoriteByTeamId($user->getId(), $teamId);

        return $favoriteEntity instanceof Favorite;
    }

    public function userFavoriteUp(User $user, int $teamId): void
    {
        $userId = $user->getId();
        $userFavoriteEntity = $this->userFavoriteRepository->getUserFavoriteByTeamId($userId, $teamId);

        if ($userFavoriteEntity instanceof Favorite) {
            $favoritePosition = $userFavoriteEntity->getFavoritePosition();
            $firstPosition = $this->userFavoriteRepository->getUserFavoritesFirstPosition($user->getId());


            if (false !== $firstPosition && $firstPosition < $favoritePosition) {

                $positionToChange = $this->userFavoriteRepository->getFavoritePositionAboveCurrentPosition(
                    $userId,
                    $favoritePosition
                );
              //  throw new \Exception($positionToChange + "position");
                $positionEntityToChange = $this->userFavoriteRepository->getUserFavoriteEntityByPosition(
                    $userId,
                    $positionToChange
                );
                if ($positionEntityToChange instanceof Favorite) {
                    $this->userFavoriteEntityManager->updateUserFavoritePosition(
                        $userFavoriteEntity,
                        $positionEntityToChange,
                    );
                }
            }
        }
    }

    public function userFavoriteDown(User $user, int $teamId): void
    {
        $userId = $user->getId();
        $userFavoriteEntity = $this->userFavoriteRepository->getUserFavoriteByTeamId($userId, $teamId);

        if ($userFavoriteEntity instanceof Favorite) {
            $favoritePosition = $userFavoriteEntity->getFavoritePosition();
            $lastPosition = $this->userFavoriteRepository->getUserFavoritesLastPosition($userId);

            if (false !== $lastPosition && $lastPosition > $favoritePosition) {
                $positionToChange = $this->userFavoriteRepository->getFavoritePositionBelowCurrentPosition(
                    $userId,
                    $favoritePosition
                );
                $positionEntityToChange = $this->userFavoriteRepository->getUserFavoriteEntityByPosition(
                    $userId,
                    $positionToChange
                );
                if ($positionEntityToChange instanceof Favorite) {
                    $this->userFavoriteEntityManager->updateUserFavoritePosition(
                        $userFavoriteEntity,
                        $positionEntityToChange,
                    );
                }
            }
        }
    }
}
