<?php

declare(strict_types=1);

namespace App\Components\Shop\Communication\Controller;

use App\Components\Shop\Business\ProductBusinessFacade;
use App\Components\User\Business\UserBusinessFacadeInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CartPageController extends AbstractController
{
    public function __construct(
        private readonly UserBusinessFacadeInterface $userBusinessFacade,
        private readonly ProductBusinessFacade $productBusinessFacade,
    ) {
    }

    #[Route('/cart-page', name: 'cart-page')]
    public function index(): JsonResponse
    {
        $userSecurity = $this->getUser();
        if (!$userSecurity instanceof UserInterface) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Unauthorized',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userBusinessFacade->getUserEntity($userSecurity);

        if (!$user instanceof User) {
            return new JsonResponse([
                'status' => false,
                'message' => 'User not found',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $products = $this->productBusinessFacade->getProducts($user);

        if (empty($products)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'No products found',
            ], Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse([
            'status' => 'success',
            'products' => $products,
        ], Response::HTTP_OK);
    }

}
