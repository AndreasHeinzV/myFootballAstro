<?php

declare(strict_types=1);

namespace App\Components\Shop\Business\Model;

use App\Components\Shop\Persistence\Dtos\ProductDto;
use App\Components\Shop\Persistence\ProductEntityManager;
use App\Components\Shop\Persistence\ProductRepository;
use App\Entity\Product;
use App\Entity\User;

class ProductManager
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductEntityManager $entityManager,
    ) {
    }

    public function addProductToCart(User $user, ProductDto $productDto): void
    {
        $productEntity = $this->productRepository->getProductEntityByName($user, $productDto->name);
        if ($productEntity instanceof Product) {
            $this->entityManager->manipulateProductEntityAmount($productEntity, $productDto->amount);
        } else {
            $this->entityManager->saveProduct($productDto, $user);
        }
    }

    public function increaseProductQuantity(User $user, string $productName): void
    {
        $productEntity = $this->productRepository->getProductEntityByName($user, $productName);
        if ($productEntity instanceof Product) {
            $this->entityManager->manipulateProductEntityAmount($productEntity, +1);
        }
    }

    public function decreaseProductQuantity(User $user, string $productName): void
    {
        $productEntity = $this->productRepository->getProductEntityByName($user, $productName);
        if ($productEntity instanceof Product) {
            if ($productEntity->getAmount() > 1) {
                $this->entityManager->manipulateProductEntityAmount($productEntity, -1);
            } else {
                $this->deleteProductFromCart($user, $productName);
            }
        }
    }

    public function deleteProductFromCart(User $user, string $productName): void
    {

        $productEntity = $this->productRepository->getProductEntityByName($user, $productName);
        if ($productEntity instanceof Product) {
            $this->entityManager->deleteProduct($user, $productEntity->getId());
        }
    }
}
