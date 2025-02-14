<?php

declare(strict_types=1);

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use App\Components\User\Business\UserBusinessFacadeInterface;
use App\Components\UserFavorite\Business\UserFavoriteBusinessFacadeInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/teamDetails/')]
class TeamDetailsManipulationController extends AbstractController
{
    public function __construct(
        private readonly FootballBusinessFacadeInterface $footballBusinessFacade,
        private readonly UserFavoriteBusinessFacadeInterface $userFavoriteBusinessFacade,
        private readonly Security $security,
        private readonly UserBusinessFacadeInterface $userBusinessFacade,
    ) {
    }

    #[Route('favoriteStatus/{teamId}', name: 'team_details_status', methods: ['GET'])]
    public function getStatus(string $teamId): JsonResponse
    {
        $user = $this->getUser();
        $userEntity = ($user instanceof UserInterface) ? $this->userBusinessFacade->getUserEntity($user) : null;
        $message = '';
        if (!$user instanceof UserInterface) {
            $message = ' security is broken';
        }
        if (!$user) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Unauthorized cant find user' . $message,
            ], Response::HTTP_UNAUTHORIZED);
        }
        if (!$userEntity) {
            return new JsonResponse([
                'status' => false,
                'message' => 'User not found',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $favoriteStatus = $this->userFavoriteBusinessFacade->getFavoriteStatus($userEntity, (int)$teamId);

        return new JsonResponse([
            'status' => $favoriteStatus, // Ensures the structure matches Astro expectations
            'userName' => $userEntity->getUserIdentifier(),
        ], Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('add', name: 'team_details_add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
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

        $this->userFavoriteBusinessFacade->addFavorite($user, (int)$data['teamId']);

        return new JsonResponse([
            'status' => true,
        ], Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }


    #[Route('remove', name: 'team_details_delete', methods: ['POST'])]
    public function remove(Request $request): JsonResponse
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

        $this->userFavoriteBusinessFacade->removeFavorite($user, (int)$data['teamId']);

        return new JsonResponse([
            'status' => true,
        ], Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}