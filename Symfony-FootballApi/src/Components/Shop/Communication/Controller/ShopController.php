<?php

declare(strict_types=1);

namespace App\Components\Shop\Communication\Controller;

use App\Components\Shop\Business\ProductBusinessFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    public function __construct(private readonly ProductBusinessFacade $productBusiness)
    {
    }

    #[Route('/shop/{teamName}/{teamId}', name: 'club_shop')]
    public function index(int $teamId, string $teamName): Response
    {
        $products = $this->productBusiness->getClubProducts($teamId);

        return $this->render('shop/shop.html.twig', [
            'products' => $products,
            'teamName' => $teamName,
            'teamId' => $teamId,
        ]);
    }
}
