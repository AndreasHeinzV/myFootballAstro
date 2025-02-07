<?php

namespace App\Components\Football\Communication\Controller;

use App\Components\Football\Business\FootballBusinessFacadeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PlayerController extends AbstractController
{
    public function __construct(private readonly FootballBusinessFacadeInterface $footballBusinessFacade)
    {
    }
    #[Route('player/{playerName}/{id}', name: 'player_details')]
   // #[Route('team/{teamName}/player/{playerName}', name: 'player_details')]
    public function index(Request $request): Response
    {


        return $this->render('football/player_details.html.twig', [
            'playerData' => $this->footballBusinessFacade->getPlayer($request->query->get('id')),
        ]);
    }
}
