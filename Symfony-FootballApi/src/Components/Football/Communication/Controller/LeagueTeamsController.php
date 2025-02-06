<?php

declare(strict_types=1);

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/league')]
class LeagueTeamsController extends AbstractController
{
    public function __construct(private readonly FootballBusinessFacadeInterface $footballBusinessFacade)
    {
    }

    #[Route('/{leagueName}/{leagueId<[A-Za-z0-9-]+>}', name: 'league_detail')]
    public function index(string $leagueId): Response
    {

        return $this->render(
            'football/leagueTeams.html.twig',
            ['teams' => $this->footballBusinessFacade->getLeagueTeams($leagueId)]
        );
    }
}
