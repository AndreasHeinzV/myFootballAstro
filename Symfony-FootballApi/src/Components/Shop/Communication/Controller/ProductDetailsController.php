<?php

declare(strict_types=1);

namespace App\Components\Shop\Communication\Controller;

use App\Components\Shop\Business\ProductBusinessFacade;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductDetailsController extends AbstractController
{
    public function __construct(readonly private ProductBusinessFacade $productBusiness)
    {
    }

    #[Route('/product-details/{teamName}/{category}/{name}/{teamId}', name: 'product_details', requirements: ['imageLink' => '.+'])]
    public function index(string $teamName, string $category, string $name, int $teamId): Response
    {
        $productDto = $this->productBusiness->getProductDto($teamId, $category, $name);

        return $this->render(
            'shop/details.html.twig',
            ['category' => $category, 'name' => $name, 'teamName' => $teamName]
        );
    }
}
