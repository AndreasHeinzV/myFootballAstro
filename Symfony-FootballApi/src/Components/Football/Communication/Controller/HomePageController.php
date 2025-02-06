<?php

declare(strict_types=1);

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    public function __construct(private readonly FootballBusinessFacadeInterface $footballBusinessFacade)
    {
    }

    #[Route('/league', name: 'leaguePage')]
    public function homePageController(): Response
    {
        return $this->render('football/leaguesPage.html.twig', ['leagues' => $this->footballBusinessFacade->getLeagues()]);
    }
}
