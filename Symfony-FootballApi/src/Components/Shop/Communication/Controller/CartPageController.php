<?php

declare(strict_types=1);

namespace App\Components\Shop\Communication\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartPageController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/cart-page', name: 'cart-page')]
    public function index(): Response
    {
        $products = null;

        return $this->render('cart/cartPage.html.twig', ['products' => $products]);
    }

}
