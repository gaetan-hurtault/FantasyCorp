<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function calculProductRating(Product $product)
    {
        $positif = $this->createQueryBuilder('c')
        ->select('SUM(c.id)')
        ->andWhere('c.product = :product')
        ->setParameter('product', $product)
        ->andWhere('c.recommended = true')
        ->getQuery()
        ->getSingleScalarResult();

        $negatif = $this->createQueryBuilder('c')
        ->select('SUM(c.id)')
        ->andWhere('c.product = :product')
        ->setParameter('product', $product)
        ->andWhere('c.recommended = false')
        ->getQuery()
        ->getSingleScalarResult();

        $rating = round(($positif * 5 / ( $negatif + $positif )),2,PHP_ROUND_HALF_UP);
        return $product->setRating($rating);
    }
    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
