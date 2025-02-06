<?php

declare(strict_types=1);

namespace App\Tests\Components\Shop\Persistence;

use App\Components\Shop\Persistence\Dtos\ProductDto;
use App\Components\Shop\Persistence\ProductEntityManager;
use App\Components\Shop\Persistence\ProductRepository;
use App\Components\User\Persistence\UserRepository;
use App\Entity\Product;
use App\Entity\User;
use App\Tests\BaseTestCase;
use App\Tests\Fixtures\Config;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;

class ProductRepositoryTest extends BaseTestCase
{
    private ProductEntityManager $entityManager;
    private UserRepository $userRepository;

    private ProductRepository $productRepository;
    private User $user;

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

        $productDto = new ProductDto('scarf', 'clubName', 'imageLink', 'scarf', 'L', 40, 'link', 3);
        $this->user = $this->userRepository->findUserByMail(Config::USER_EMAIL_ONE);
        $this->entityManager->saveProduct($productDto, $this->user);
    }

    public function testGetExistingProduct(): void
    {
        $product = $this->productRepository->getProductEntityById($this->user, 1);
        self::assertInstanceOf(Product::class, $product);
        self::assertSame('scarf', $product->getProductName());
    }

    public function testNotExistingProduct(): void
    {
        $product = $this->productRepository->getProductEntityById($this->user, 2);
        self::assertNull($product);
    }

    public function testGetAllProducts(): void
    {
        $products = $this->productRepository->getProductEntities($this->user);
        self::assertCount(1, $products);
    }

    public function testGetAllProductsWithNoProducts(): void
    {
        $userWithoutProducts = $this->userRepository->findUserByMail(Config::USER_EMAIL_TWO);
        $products = $this->productRepository->getProductEntities($userWithoutProducts);
        self::assertCount(0, $products);
    }
}
