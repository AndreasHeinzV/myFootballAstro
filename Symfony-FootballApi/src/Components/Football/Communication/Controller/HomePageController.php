<?php

declare(strict_types=1);

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    public function __construct(private readonly FootballBusinessFacadeInterface $footballBusinessFacade)
    {
    }

    #[Route('/leagues', name: 'leaguePage', methods: ['GET'])]
    public function homePageController(): JsonResponse
    {
        $leagues = $this->footballBusinessFacade->getLeagues();

        if (empty($leagues)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No leagues found',
            ], Response::HTTP_NO_CONTENT);
        }

        $leaguesArray = array_map(fn ($league) => [
            'id' => $league->id,
            'leagueId' => $league->leagueId,
            'name' => $league->name,
        ], $leagues);

        return new JsonResponse([
            'status' => 'success',
            'data' => $leaguesArray,
        ], Response::HTTP_OK);
    }
}
