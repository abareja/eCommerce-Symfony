<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function search($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name LIKE :val')
            ->setParameter('val', '%'.$value.'%')
            ->getQuery()
            ->getResult()
        ;
    }
    //SELECT p.id, COUNT(*) FROM `order` o INNER JOIN order_item oi ON o.id = oi.order_ref_id INNER JOIN product p ON oi.product_id = p.id WHERE status="order" GROUP BY oi.product_id

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function bestsellers($limit)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT p
            FROM App\Entity\Order o 
            INNER JOIN App\Entity\OrderItem oi WITH o.id = oi.orderRef 
            INNER JOIN App\Entity\Product p WITH oi.product = p.id 
            WHERE o.status='order'
            GROUP BY oi.product
            ORDER BY COUNT(p.id) DESC"
        )
        ->setMaxResults($limit);

        return $query->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
