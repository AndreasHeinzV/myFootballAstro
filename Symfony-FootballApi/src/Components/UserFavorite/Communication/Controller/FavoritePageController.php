<?php

declare(strict_types=1);

namespace App\Components\UserFavorite\Communication\Controller;

use App\Components\User\Business\UserBusinessFacadeInterface;
use App\Components\UserFavorite\Business\UserFavoriteBusinessFacade;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
    public function index(): JsonResponse
    {
        $userSecurity = $this->getUser();
        if (!$userSecurity instanceof UserInterface) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = $this->userBusinessFacade->getUserEntity($userSecurity);


        if (!$user instanceof User) {
            return new JsonResponse([
                'status' => false,
                'message' => 'User not found',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $favorites = $this->userFavoriteBusiness->getUserFavorites($user);

        if (empty($favorites)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No teams found',
            ], Response::HTTP_NO_CONTENT);
        }

        return $this->json($favorites, Response::HTTP_OK);
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

    #[Route('/moveUp/', name: 'favorite-page_move_up', methods: ['POST'])]
    public function moveUp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $userInterface = $this->getUser();

        if (!isset($data['teamId'])) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Missing teamId',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$userInterface instanceof UserInterface) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Unauthorized cant find user',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userBusinessFacade->getUserEntity($userInterface);
        if (!$user instanceof User) {
            return new JsonResponse([
                'status' => false,
                'message' => 'User not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }


            $this->userFavoriteBusiness->moveUpFavorite($user, (int) $data['teamId']);


        return new JsonResponse([
            'status' => true,
        ], Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/moveDown/', name: 'favorite-page_move_down', methods: ['POST'])]
    public function moveDown(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $userInterface = $this->getUser();

        if (!isset($data['teamId'])) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Missing teamId',
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$userInterface instanceof UserInterface) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Unauthorized cant find user',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userBusinessFacade->getUserEntity($userInterface);
        if (!$user instanceof User) {
            return new JsonResponse([
                'status' => false,
                'message' => 'User not authorized',
            ], Response::HTTP_UNAUTHORIZED);
        }


        $this->userFavoriteBusiness->moveDownFavorite($user, (int)$data['teamId']);

        return new JsonResponse([
            'status' => true,
        ], Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
