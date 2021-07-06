<?php

namespace App\Repository;

use App\Entity\CommandLine;
use App\Entity\Product;
use App\Entity\Promotion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Promotion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Promotion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Promotion[]    findAll()
 * @method Promotion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromotionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Promotion::class);
    }

    public function pricePromotionByProduct(Product $product)
    {
        $promotions = $product->getPromotions();
        $date = new \DateTime('@'.strtotime('now'));
        $price = 0;

        foreach ($promotions as $promotion) {
            if (($promotion->getDateBegin() <= $date ) 
            && ($promotion->getDateEnd() >= $date ) 
            && ($promotion->getNumberProduct() == 1))
            {
                $price = round(($product->getPrice() * (1-($promotion->getValue()/100))) , 2);
            }
        }

        return $price;
    }

    public function pricePromotionByCommandLine(CommandLine $commandLine)
    {
        $promotions = $commandLine->getProduct()->getPromotions();
        $date = new \DateTime('@'.strtotime('now'));
        $price = 0;
        
        foreach ($promotions as $promotion) {
            if (($promotion->getDateBegin() <= $date ) 
            && ($promotion->getDateEnd() >= $date ) 
            && ($promotion->getNumberProduct() <= $commandLine->getQuantity()))
            {
                $price = $commandLine->getProduct()->getPrice() * (1-($promotion->getValue()/100));
            }
        }

        if ($price == 0)
        {
            $price = $commandLine->getProduct()->getPrice();
        }

        return $price;
    }
    // /**
    //  * @return Promotion[] Returns an array of Promotion objects
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
    public function findOneBySomeField($value): ?Promotion
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
