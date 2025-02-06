<?php

declare(strict_types=1);

namespace App\Tests\Components\Shop\Persistence;

use App\Components\Shop\Persistence\Dtos\ProductDto;
use App\Components\Shop\Persistence\ProductEntityManager;
use App\Components\Shop\Persistence\ProductRepository;
use App\Components\User\Persistence\UserRepository;
use App\Entity\Product;
use App\Tests\BaseTestCase;
use App\Tests\Fixtures\Config;
use Doctrine\ORM\EntityManagerInterface;

class ProductEntityManagerTest extends BaseTestCase
{
    private ProductEntityManager $entityManager;
    private UserRepository $userRepository;

    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();

        $this->productRepository = $container->get(ProductRepository::class);
        $this->entityManager = new ProductEntityManager(
            $container->get(EntityManagerInterface::class),
            $this->productRepository
        );
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function testSaveProductEntity(): void
    {
        $productDto = new ProductDto('scarf', 'clubName', 'imageLink', 'scarf', 'L', 40, 'link', 3);
        $user = $this->userRepository->findUserByMail(Config::USER_EMAIL_ONE);
        $this->entityManager->saveProduct($productDto, $user);
        $product = $this->productRepository->getProductEntityById($user, 1);
        self::assertInstanceOf(Product::class, $product);
        self::assertSame('scarf', $product->getProductName());
    }

    public function testUpdateProductEntityAmount(): void
    {
        $productDto = new ProductDto('scarf', 'clubName', 'imageLink', 'scarf', 'L', 40, 'link', 3);
        $user = $this->userRepository->findUserByMail(Config::USER_EMAIL_ONE);
        $this->entityManager->saveProduct($productDto, $user);
        $product = $this->productRepository->getProductEntityById($user, 1);
        $amount = $product->getAmount();
        self::assertSame(3, $amount);

        $this->entityManager->updateProductAmount($product, 4);
        $productAfter = $this->productRepository->getProductEntityById($user, 1);

        self::assertSame('scarf', $product->getProductName());
        self::assertSame(7, $productAfter->getAmount());
    }

    public function testDeleteProductEntity(): void
    {
        $productDto = new ProductDto('scarf', 'clubName', 'imageLink', 'scarf', 'L', 40, 'link', 3);
        $user = $this->userRepository->findUserByMail(Config::USER_EMAIL_ONE);
        $this->entityManager->saveProduct($productDto, $user);
        $product = $this->productRepository->getProductEntityById($user, 1);
        self::assertInstanceOf(Product::class, $product);
        $this->entityManager->deleteProduct($user, $product->getId());
        $productAfter = $this->productRepository->getProductEntityById($user, 1);
        self::assertNull($productAfter);
    }
}
