<?php

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use App\Components\User\Business\UserBusinessFacadeInterface;
use App\Components\UserFavorite\Business\UserFavoriteBusinessFacadeInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/team/{teamName}/{teamId}')]
class TeamDetailsController extends AbstractController
{
    public function __construct(
        private readonly FootballBusinessFacadeInterface $footballBusinessFacade,
        private readonly UserFavoriteBusinessFacadeInterface $userFavoriteBusinessFacade,
        private readonly Security $security,
        private readonly UserBusinessFacadeInterface $userBusinessFacade,
    ) {
    }

    #[Route('', name: 'team_details')]
    public function index(string $teamId): JsonResponse
    {
        /*
        $user = $this->security->getUser();
        $userEntity = ($user instanceof UserInterface) ? $this->userBusinessFacade->getUserEntity($user) : null;
        $status = null !== $userEntity;

        $favoriteStatus = $status ? $this->userFavoriteBusinessFacade->getFavoriteStatus($user, $teamId) : null;
*/
        $players = $this->footballBusinessFacade->getTeam($teamId);

        if (empty($players)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No player found',
            ], Response::HTTP_NO_CONTENT);
        }
                $playersNew= $players['squad'];

        $playersArray = array_map(fn ($playersNew)=> [

                'playerId' => $playersNew->playerID,
                'link' => $playersNew->link,
                'name' => $playersNew->name,
            ], $playersNew);

        return new JsonResponse([
            'status' => 'success',
            'players' => $playersArray,
        ], Response::HTTP_OK);
    }
/*
    #[Route('/add/', name: 'team_details_add')]
    public function add(string $teamId, string $teamName): Response
    {
        // dd($request->attributes->get('teamId'));
        $user = $this->userBusinessFacade->getUserEntity($this->security->getUser());
        if ($user instanceof User) {
            $this->userFavoriteBusinessFacade->addFavorite($user, $teamId);
        }

        return $this->redirectToRoute('team_details', ['teamId' => $teamId, 'teamName' => $teamName]);
    }

    #[Route('/delete/', name: 'team_details_delete')]
    public function delete(string $teamId, string $teamName): Response
    {
        $user = $this->userBusinessFacade->getUserEntity($this->security->getUser());
        if ($user instanceof User) {
            $this->userFavoriteBusinessFacade->removeFavorite($user, $teamId);
        }

        return $this->redirectToRoute('team_details', ['teamId' => $teamId, 'teamName' => $teamName]);
    }
*/
}
