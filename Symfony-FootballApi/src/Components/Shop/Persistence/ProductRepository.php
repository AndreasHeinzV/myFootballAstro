<?php

namespace App\Components\Shop\Persistence;

use App\Components\Shop\Persistence\Mapper\ProductMapper;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private ProductMapper $productMapper)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getProductEntities(User $user): array
    {
        $productEntities = $this->findBy(['userId' => $user->getId()]);

        if (empty($productEntities)) {
            return [];
        }
        $productDtoEntities = [];
        foreach ($productEntities as $productEntity) {
            $productDtoEntities[] = $this->productMapper->createProductDto(
                $productEntity->getCategory(),
                $productEntity->getTeamName(),
                $productEntity->getProductName(),
                $productEntity->getImageLink(),
                $productEntity->getSize(),
                $productEntity->getAmount(),
                $productEntity->getPrice()
            );
        }

        return $productDtoEntities;
    }

    public function getProductEntityById(User $user, int $id): ?Product
    {
        return $this->findOneBy(['userId' => $user->getId(), 'id' => $id]);
    }

    public function getProductEntityByName(User $user, string $name): ?Product
    {
        return $this->findOneBy(['productName' => $name, 'userId' => $user->getId()]);
    }
}
