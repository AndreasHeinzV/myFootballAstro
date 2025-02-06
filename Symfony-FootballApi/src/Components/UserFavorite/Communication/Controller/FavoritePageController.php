<?php

declare(strict_types=1);

namespace App\Components\UserFavorite\Communication\Controller;

use App\Components\User\Business\UserBusinessFacadeInterface;
use App\Components\UserFavorite\Business\UserFavoriteBusinessFacade;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/favorite-page')]
class FavoritePageController extends AbstractController
{
    public function __construct(
        private readonly UserFavoriteBusinessFacade $userFavoriteBusiness,
        private readonly UserBusinessFacadeInterface $userBusinessFacade,
        private readonly Security $security,
    ) {
    }

    #[Route('', name: 'favorite-page')]
    public function index(): Response
    {
        $user = $this->userBusinessFacade->getUserEntity($this->security->getUser());

        if ($user instanceof User) {
            return $this->render(
                'favorite/favoritePage.html.twig',
                ['favorites' => $this->userFavoriteBusiness->getUserFavorites($user)]
            );
        }

        return $this->redirectToRoute('app_login');
    }

    #[Route('/delete/{teamId}', name: 'favorite-page_delete', methods: ['POST'])]
    public function removeFavorite(int $teamId): Response
    {
        $user = $this->userBusinessFacade->getUserEntity($this->security->getUser());
        if ($user instanceof User) {
            $this->userFavoriteBusiness->removeFavorite($user, $teamId);
        }

        return $this->redirectToRoute('favorite-page');
    }

    #[Route('/move-up/{teamId}', name: 'favorite-page_move_up', methods: ['POST'])]
    public function moveUp(int $teamId): Response
    {
        $user = $this->userBusinessFacade->getUserEntity($this->security->getUser());
        if ($user instanceof User) {
            $this->userFavoriteBusiness->moveUpFavorite($user, $teamId);
        }
        return $this->redirectToRoute('favorite-page');
    }

    #[Route('/move-down/{teamId}', name: 'favorite-page_move_down', methods: ['POST'])]
    public function moveDown(int $teamId): Response
    {
        $user = $this->userBusinessFacade->getUserEntity($this->security->getUser());
        if ($user instanceof User) {
            $this->userFavoriteBusiness->moveDownFavorite($user, $teamId);
        }
        return $this->redirectToRoute('favorite-page');
    }
}
