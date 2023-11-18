<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, Product::class);
        $this->manager = $manager;
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

    public function saveProduct(Product $product): void
    {
        $entityManager = $this->getEntityManager();

        // Si el ID es null, es un nuevo producto y debe ser persistido
        if ($product->getId() === null) {
            $entityManager->persist($product);
        }

        // Realizar cambios en la base de datos
        $entityManager->flush();
    }

    public function updateProduct(Product $product): Product
    {
        $this->manager->persist($product);
        $this->manager->flush();

        return $product;
    }

    public function updateProducts(Product $products): array
    {
        $updatedProducts = [];

        foreach ($products as $product) {
            if (!$product instanceof Product) {
                throw new \InvalidArgumentException('El elemento en el array no es una instancia válida de Product.');
            }

            $this->manager->persist($product);

            $updatedProducts[] = $product;
        }

        $this->manager->flush();

        return $updatedProducts;
    }

    public function removeProduct(Product $product)
    {
        $this->manager->remove($product);
        $this->manager->flush();
    }
}
