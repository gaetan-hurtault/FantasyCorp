<?php
namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Repository\CategoryRepository;

class ProductFixtures extends Fixture
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository )
    {
        $this->categoryRepository = $categoryRepository;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR_fr');

        for($i = 1; $i <= 30; $i++)
        {
            $product = new Product;
            $category = $this->categoryRepository->findOneBy(mt_rand(1,12));

            $product->setTitle($faker->sentence())
                    ->setQuantity($faker->randomNumber(2))
                    ->setOnline(1)
                    ->setCategory($category)
                    ->setDateAdd(new \DateTime())
                    ->setProductCondition('neuf')
                    ->setExcluWeb(mt_rand(0,1))
                    ->setPricePurchasing($faker->randomFloat(2,10,200))
                    ->setPrice($product->getPricePurchasing() * 2)
                    ->setLength($faker->randomNumber(3))
                    ->setWidth($faker->randomNumber(3))
                    ->setHeight($faker->randomNumber(3))
                    ->setWeight($faker->randomNumber(4))
                    ->setDescription($faker->text($maxNbChars = 1000) )
                    ->setDescriptionFast($faker->text($maxNbChars = 300) )
                    ;
                    
         $manager->persist($product);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
