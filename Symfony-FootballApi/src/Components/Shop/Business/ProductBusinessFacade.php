<?php

declare(strict_types=1);

namespace App\Components\Shop\Business;

use App\Components\Shop\Business\Model\CalculatePrice;
use App\Components\Shop\Business\Model\CreateProducts;
use App\Components\Shop\Business\Model\ProductManager;
use App\Components\Shop\Persistence\Dtos\ProductDto;
use App\Components\Shop\Persistence\Mapper\ProductMapper;
use App\Components\Shop\Persistence\ProductRepository;
use App\Entity\User;

readonly class ProductBusinessFacade
{
    public function __construct(
        private CreateProducts $createProducts,
        private CalculatePrice $calculatePrice,
        private ProductMapper $productMapper,
        private ProductManager $productManager,
        private ProductRepository $productRepository,
    ) {
    }

    public function getClubProducts(int $teamId): array
    {
        return $this->createProducts->createProducts($teamId);
    }

    public function getProductDto(int $teamId, string $category, $productName): ?ProductDto
    {
        return $this->createProducts->createProduct($teamId, $category, $productName);
    }

    public function createProduct(
        string $category,
        string $teamName,
        string $name,
        string $image,
        ?string $size,
        ?int $amount,
    ): ProductDto {
        return $this->productMapper->createProductDto($category, $teamName, $name, $image, $size, $amount, null);
    }

    public function getProductPrice(ProductDto $productDto): ProductDto
    {
        return $this->calculatePrice->calculateProductPrice($productDto);
    }

    public function AddProductToCart(User $user, ProductDto $productDto): void
    {
        $this->productManager->addProductToCart($user, $productDto);
    }

    public function getProducts(User $user): ?array
    {
        return $this->productRepository->getProductEntities($user);
    }

    public function increaseProductQuantity(User $user, string $productName): void
    {
        $this->productManager->increaseProductQuantity($user, $productName);
    }

    public function decreaseProductQuantity(User $user, string $productName): void
    {
        $this->productManager->decreaseProductQuantity($user, $productName);
    }

    public function deleteProduct(User $user, string $productName): void
    {
        $this->productManager->deleteProductFromCart($user, $productName);
    }
}
