<?php

declare(strict_types=1);

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/league')]
class LeagueTeamsController extends AbstractController
{
    public function __construct(private readonly FootballBusinessFacadeInterface $footballBusinessFacade)
    {
    }

    #[Route('/{leagueName}/{leagueId<[A-Za-z0-9-]+>}', name: 'league_detail')]
    public function index(string $leagueId): JsonResponse
    {
        $leagueTeams = $this->footballBusinessFacade->getLeagueTeams($leagueId);

        if (empty($leagueTeams)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No teams found',
            ], Response::HTTP_NO_CONTENT);
        }

        $leaguesArray = array_map(fn ($leagueTeams) => [
            'position' => $leagueTeams->position,
            'name' => $leagueTeams->name,
            'teamId' => $leagueTeams->teamId,
            'playedGames' => $leagueTeams->playedGames,
            'won' => $leagueTeams->won,
            'draw' => $leagueTeams->draw,
            'lost' => $leagueTeams->lost,
            'points' => $leagueTeams->points,
            'goalsFor' => $leagueTeams->goalsFor,
            'goalsAgainst' => $leagueTeams->goalsAgainst,
            'goalDifference' => $leagueTeams->goalDifference,
        ], $leagueTeams);



        return new JsonResponse([
            'status' => 'success',
            'data' => $leaguesArray,
        ], Response::HTTP_OK);
    }
}
