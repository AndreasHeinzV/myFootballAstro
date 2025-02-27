<?php

declare(strict_types=1);

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlayerController extends AbstractController
{
    public function __construct(private readonly FootballBusinessFacadeInterface $footballBusinessFacade)
    {
    }

    #[Route('/player/{playerName}/{playerId}', name: 'player_details')]
    // #[Route('team/{teamName}/player/{playerName}', name: 'player_details')]
    public function index(string $playerId): JsonResponse {

        $player = $this->footballBusinessFacade->getPlayer($playerId);


        if (null === $player) {
            return new JsonResponse([
                'success' => false,
                'message' => 'No player found.',
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = [
            'name' => $player->name,
            'position' => $player->position,
            'dateOfBirth' => $player->dateOfBirth,
            'nationality' => $player->nationality,
            'shirtNumber' => $player->shirtNumber,
        ];
        return new JsonResponse([
            'status' => 'success',
            'data' => $data,
        ], Response::HTTP_OK);
    }
}
