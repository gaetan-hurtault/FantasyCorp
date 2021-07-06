<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\SearchProduct;
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
    private $promotionRepository;
    
    public function __construct(ManagerRegistry $registry, PromotionRepository $promotionRepository)
    {
        parent::__construct($registry, Product::class);
        $this->promotionRepository = $promotionRepository;
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
    //  */
    public function findProduct(SearchProduct $search = null,$max = false)
    {
        $query = $this->createQueryBuilder('p');
        if($search != null)
        {
            $categories = $search->getCategories();
            //Gestion de la partie catégorie
            if(!empty($categories))
            {
                foreach ($categories as $key => $category) {  
                    $query->orWhere('p.category = :categorym'.$key)
                    ->setParameter('categorym'.$key, $category);
    
                    if($category->getCategories() != null)
                    {
                        foreach ($category->getCategories() as $key => $value) {
                                $query->orWhere('p.category = :category'.$key)
                                ->setParameter('category'.$key, $value);
                        }
                    }
                }
            }
            // Gestion de la partie tri
            if(!empty($search->getTri()))
            {
            switch ($search->getTri()) {
                case 'asc':
                    $query->orderBy('p.price', 'ASC');
                    break;
                case 'desc':
                    $query->orderBy('p.price', 'DESC');
                    break;
                case 'alpha':
                    $query->orderBy('p.title', 'ASC');
                    break;
                case 'bestRating':
                    $query->orderBy('p.rating', 'DESC');
                    break;
                case 'lowRating':
                    $query->orderBy('p.rating', 'ASC');
                    break;
                default:
                    $query->orderBy('p.id', 'ASC');
                    break;
                }
            }
            else
            {
                $query->orderBy('p.id', 'ASC');
            }

            //Gestion de la partie recherche
            if(!empty($search->getSearch()))
            {
                $query->andWhere('p.title LIKE :title')
                ->setParameter('title', '%'.$search->getSearch().'%');
            }
            //Gestion de l'occasion
            if(!empty($search->getCondition()))
            {
                $query->andWhere('p.productCondition = :condition')
                ->setParameter('condition', $search->getCondition());
            }
            //Gestion Prix
            if(!empty($search->getPrixMin()))
            {
                $query->andWhere('p.price >= :prixmin')
                ->setParameter('prixmin', $search->getPrixMin());
            }
            if(!empty($search->getPrixMax()))
            {
                $query->andWhere('p.price <= :prixmax')
                ->setParameter('prixmax', $search->getPrixMax());
            }
            //Gestion Editeur
            if(!empty($search->getEditor()))
            {
                $query->andWhere('p.editor = :editor')
                ->setParameter('editor', $search->getEditor());
            }
            //Gestion Age Minimum
            if(!empty($search->getAgeMin()))
            {
                $query->andWhere('p.ageMin <= :ageMin')
                ->setParameter('ageMin', $search->getAgeMin());
            }
            //Gestion Temps
            if(!empty($search->getTimeMin()))
            {
                $query->andWhere('p.timePlayingMin <= :timeMin')
                ->andWhere('p.timePlayingMax >= :timeMin')
                ->setParameter('timeMin', $search->getTimeMin());
            }
            if(!empty($search->getTimeMax()))
            {
                $query->andWhere('p.timePlayingMin <= :timeMax')
                ->andWhere('p.timePlayingMax >= :timeMax')
                ->setParameter('timeMax', $search->getTimeMax());
            }
            //Gestion Joueur
            if(!empty($search->getNbrPlayerMin()))
            {
                $query->andWhere('p.playerNumberMin <= :nbrPlayerMin')
                ->andWhere('p.playerNumberMax >= :nbrPlayerMin')
                ->setParameter('nbrPlayerMin', $search->getNbrPlayerMin());
            }
            if(!empty($search->getNbrPlayerMax()))
            {
                $query->andWhere('p.playerNumberMax >= :nbrPlayerMax')
                ->andWhere('p.playerNumberMin <= :nbrPlayerMax')
                ->setParameter('nbrPlayerMax', $search->getNbrPlayerMax());
            }
            //Gestion Theme
            if(!empty($search->getThemes()))
            {
                foreach ($search->getThemes() as $key => $value) {
                    $query->andWhere(':theme'.$key.' MEMBER OF p.themes')
                    ->setParameter('theme'.$key, $value);
                }
            }
            //Gestion Langues
            if(!empty($search->getLanguages()))
            {    
                foreach ($search->getLanguages() as $key => $value) {
                    $query->andWhere(':language'.$key.' MEMBER OF p.language')
                    ->setParameter('language'.$key, $value);
                }
            }

            if (!empty($search->getTag()))
            {
                switch ($search->getTag()) {
                    case 'nouveau':
                        $query->andWhere('p.dateAdd > :dateNouveau')
                        ->setParameter('dateNouveau', new \DateTime('@'.strtotime('-10 day')));
                        break;
                    case 'nouveaute':
                        $query->andWhere('p.dateAdd > :dateNouveaute')
                        ->setParameter('dateNouveaute', new \DateTime('@'.strtotime('-1 month')));
                        break;
                    case 'promotion':
                        $query->leftJoin('App\Entity\Promotion','pr','WITH','p.id = pr.product')
                        ->andWhere('pr.dateBegin <= :datePromo')
                        ->andWhere('pr.dateEnd >= :datePromo')
                        ->setParameter('datePromo', new \DateTime('@'.strtotime('now')));
                        break;
                    default:
                        break;
                }
            }
        }
        
        if($max == true)
        {
            $query->setMaxResults(10);
        }
            
        return $query
        ->andWhere('p.online = :online')
        ->setParameter('online', 1)
        ->getQuery()
        ->getResult()
        ;

    }

    public function findProductWithPromo($search = null,$max = false)
    {
        $data = array();

        $products = $this->findProduct($search,$max);
        foreach ($products as $product) {
            array_push($data,["product" => $product , "promo" => $this->promotionRepository->pricePromotionByProduct($product)]);
        }

        return $data;
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
