<?php

namespace App\Repository;

use App\Entity\CommandLine;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommandLine|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommandLine|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommandLine[]    findAll()
 * @method CommandLine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandLineRepository extends ServiceEntityRepository
{
    private $promotionRepository;
    private $em;

    public function __construct(ManagerRegistry $registry,EntityManagerInterface $em,PromotionRepository $promotionRepository)
    {
        parent::__construct($registry, CommandLine::class);
        $this->em = $em;
        $this->promotionRepository = $promotionRepository;
    }

    public function calculTotalPrice(CommandLine $commandLine)
    {
        $commandLine->setTotalPrice(
            $this->totalPrice($commandLine)
        );
        $this->em->flush();

        return $commandLine->getTotalPrice();
    }
    
    public function totalPrice(CommandLine $commandLine)
    {
        return round(
            $commandLine->getQuantity() * $this->promotionRepository->pricePromotionByCommandLine($commandLine)
            ,2);
    }
    // /**
    //  * @return CommandLine[] Returns an array of CommandLine objects
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
    public function findOneBySomeField($value): ?CommandLine
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
