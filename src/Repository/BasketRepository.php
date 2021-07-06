<?php

namespace App\Repository;

use App\Entity\Basket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Basket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Basket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Basket[]    findAll()
 * @method Basket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BasketRepository extends ServiceEntityRepository
{
    private $commandLineRepository;
    private $globalParameterRepository;
    private $em;

    public function __construct(CommandLineRepository $commandLineRepository, ManagerRegistry $registry,GlobalParameterRepository $globalParameterRepository,EntityManagerInterface $em)
    {
        parent::__construct($registry, Basket::class);
        $this->commandLineRepository = $commandLineRepository;
        $this->globalParameterRepository = $globalParameterRepository;
        $this->em = $em;
    }
    public function findLast()
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function calculPrice(Basket $basket)
    {
        $price = 0;

        foreach ($basket->getCommandLines() as $commandLine) {
            $price += $this->commandLineRepository->calculTotalPrice($commandLine);
        }

        $price = round($price,2);
        $basket->setPrice($price);

        $this->em->flush();

        return $basket;
    }

    public function validateStockBasket(Basket $basket)
    {
        $errorStock = array();

        foreach ($basket->getCommandLines() as $commandLine) {
            if ($commandLine->getQuantity() > $commandLine->getProduct()->getQuantity())
            {
                array_push($errorStock, $commandLine->getProduct());
                $this->em->remove($commandLine);
                $this->em->flush();
            }
        }

        $this->isEmptyBasket($basket);

        if (empty($errorStock))
        {
            return $errorStock;
        }
        else{
            return true;
        }
    }

    public function isEmptyBasket(Basket $basket)
    {
        $commandLine = $this->commandLineRepository->findByBasket($basket);

        if (empty($commandLine))
        {
            $this->em->remove($basket);
            $this->em->flush();

            unset($_SESSION['idBasket']);
            return true;
        }

        return false;
    }

    public function calculWeight(Basket $basket)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.id = :basket')
            ->setParameter('basket', $basket)
            ->leftJoin('App\Entity\CommandLine','cl','WITH','b.id = cl.basket')
            ->leftJoin('App\Entity\Product','p','WITH','cl.product = p.id')
            ->select('SUM(p.weight * cl.quantity) as weightTotal')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function calculPriceShip(Basket $basket)
    {
        $methodShipp = $basket->getMethodShipp();
        if($methodShipp != 2)
        {
            $totalWeight = intval($this->CalculWeight($basket));
                
            switch ($basket->getMethodShipp()) {
                case 0:
                    $priceGP = $this->globalParameterRepository->findOneByTitle('Mondial Relay');
                    break;
                case 1:
                    if ($basket->getUser() != null)
                    {
                        $codePostal = $basket->getUser()->getAdressBill()->getCodePostal();
                        $codePostal = settype($codePostal, "string");
            
                        if (substr($codePostal,0,2) == "97")
                        {
                            if(intval(substr($codePostal,0,3)) >= 971 && intval(substr($codePostal,0,3)) <= 978)
                            {
                                $priceGP = $this->globalParameterRepository->findOneByTitle('Colissimo France DOM-TOM1');
                            }
                            else
                            {
                                $priceGP = $this->globalParameterRepository->findOneByTitle('Colissimo France DOM-TOM2');
                            }
                        }  
                        else
                        {
                            $priceGP = $this->globalParameterRepository->findOneByTitle('Colissimo France Métropolitaine');
                        }
                    }
                    else
                    {
                        $priceGP = $this->globalParameterRepository->findOneByTitle('Colissimo France Métropolitaine');
                    }
                    break;
            }

            $priceGP = unserialize($priceGP->getContent());

            $priceShipp = 0; 

            foreach ($priceGP as $key => $value) {
                if($priceShipp == 0)
                {
                    $priceShipp = floatval($value);
                }

                if (intval($key) <= $totalWeight)
                {
                    $priceShipp = floatval($value) ;
                }
                elseif (intval($key) > $totalWeight)
                {
                    break;
                }
            }
        }
        else
        {
            $priceShipp = 0 ;
        }

        $basket->setShippingCost($priceShipp);
        $this->em->flush();

        return $basket;
    }
    // /**
    //  * @return Basket[] Returns an array of Basket objects
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
    public function findOneBySomeField($value): ?Basket
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
