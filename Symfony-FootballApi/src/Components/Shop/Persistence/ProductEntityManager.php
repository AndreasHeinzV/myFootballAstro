<?php

declare(strict_types=1);

namespace App\Components\Shop\Persistence;

use App\Components\Shop\Persistence\Dtos\ProductDto;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductEntityManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
    ) {
    }

    public function saveProduct(ProductDto $productDto, User $user): void
    {
        $product = new Product();
        $product->setTeamName($productDto->teamName);
        $product->setProductName($productDto->name);
        $product->setPrice($productDto->price);
        $product->setCategory($productDto->category);
        $product->setAmount($productDto->amount);
        $product->setUserId($user);
        $product->setSize($productDto->size);
        $product->setImageLink($productDto->imageLink);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function updateProductAmount(Product $product, int $amount): void
    {
        $product->setAmount($product->getAmount() + $amount);
        // $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    public function deleteProduct(User $user, int $id): void
    {
        $product = $this->productRepository->getProductEntityById($user, $id);
        if ($product instanceof Product) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
        }
    }
    public function manipulateProductEntityAmount(Product $productEntity, int $amount): void
    {
        $productEntity->setAmount($productEntity->getAmount() + $amount);
        $this->entityManager->persist($productEntity);
        $this->entityManager->flush();
    }


}
