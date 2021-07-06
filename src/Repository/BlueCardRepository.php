<?php

namespace App\Repository;

use App\Entity\BlueCard;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlueCard|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlueCard|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlueCard[]    findAll()
 * @method BlueCard[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlueCardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlueCard::class);
    }

    // /**
    //  * @return BlueCard[] Returns an array of BlueCard objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlueCard
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
