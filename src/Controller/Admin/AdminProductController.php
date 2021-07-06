<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Editor;
use App\Entity\Picture;
use App\Entity\Languages;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository; 
use App\Entity\Product;
use App\Entity\SearchProduct;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

class AdminProductController extends AbstractController
{
    /**
     * @var ProductRepository
     */
    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;
    private $pictureRepository;

    public function __construct(ProductRepository $repository, EntityManagerInterface $em, CategoryRepository $categoryRepository, PictureRepository $pictureRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->repository = $repository;
        $this->em = $em;
        $this->pictureRepository = $pictureRepository;
    }

    /**
     * @Route("/admin/product", name="admin.product.catalogue")
     */
    public function catalogue(Request $request,PaginatorInterface $paginator)
    {

        //Conception du formulaire de recherche
        $searchProduct = new SearchProduct;

        $form = $this->createFormBuilder($searchProduct,['method' => 'GET',])
        ->add('search', TextType::class,[
            'required' => false,
        ])
        ->add('categories', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'required' => false,
            'multiple' => true
        ])
        ->add('prixMin', NumberType::class,[
            'required' => false,
        ])
        ->add('prixMax', NumberType::class,[
            'required' => false,
        ])
        ->add('rechercher', SubmitType::class, ['label' => 'Rechercher'])
        ->getForm();

        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid())
        {
            $donnees = $this->repository->findProduct($searchProduct);
        }
        else{
            $donnees = $this->repository->findAll();
        }

        $products = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            20 // Nombre de résultats par page
        );

        return $this->render('admin/product/catalogue.html.twig', [
            'products' => $products,
            'form' =>$form->createView()
        ]);
    }
    /**
     * @Route("/admin/product/online/{id}", name="admin.product.online")
     */
    public function online(Product $product)
    {
        if($product->getOnline() == 1)
        {
            $product->setOnline(0);
        }
        else
        {
            $product->setOnline(1);
        }

        $this->em->flush();

        return $this->redirectToRoute('admin.product.catalogue');
    }
    /**
     * @Route("/admin/product/editer/{id}", name="admin.product.editer")
     */
    public function editer(Product $product, Request $request)
    {
        $form = $this->createForm(ProductType::class, $product);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // On récupère les images transmises
            $images = $form->get('images')->getData();

            // On boucle sur les images
            foreach($images as $image){
                $img = $this->pictureRepository->registerPicture($image);
                $product->addPicture($img);
            }
            
            $imageFirst = $form->get('pictureFirst')->getData();

            $pictureFirst = $product->getPictureFirst();

            if (!empty($imageFirst)){
                if(!empty($pictureFirst))
                {
                    $directory = $this->getParameter('images_directory');
                    unlink($directory.'/'.$pictureFirst->getName());
                    $this->em->remove($pictureFirst);
                }

                $img = $this->pictureRepository->registerPicture($imageFirst);
                $img->setProduct($product);
                $product->setPictureFirst($img);
            }
            if($product->getPlayerNumberMin() == null)
            {
                $product->setPlayerNumberMin(1);
            }
            if($product->getAgeMin() == null)
            {
                $product->setAgeMin(0);
            }
            $this->em->flush();
            return $this->redirectToRoute('admin.product.catalogue');
        }

        return $this->render('admin/product/editer.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
        /**
     * @Route("/admin/product/add", name="admin.product.add")
     */
    public function add(Request $request)
    {
        $product = new Product;

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            // On récupère les images transmises
            $images = $form->get('images')->getData();
            
            // On boucle sur les images
            foreach($images as $image){
                $img = $this->pictureRepository->registerPicture($image);
                $product->addPicture($img);
            }

            $imageFirst = $form->get('pictureFirst')->getData();
            $img = $this->pictureRepository->registerPicture($imageFirst);
            $img->setProduct($product);
            $product->setPictureFirst($img);

            $product->setOnline(1);
            $product->setDateAdd(new \DateTime('@'.strtotime('now')));
            $product->setRating(0);
            if($product->getPlayerNumberMin() == null)
            {
                $product->setPlayerNumberMin(1);
            }
            if($product->getAgeMin() == null)
            {
                $product->setAgeMin(0);
            }
            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('admin.product.catalogue');
        }

        return $this->render('admin/product/add.html.twig', [
            'controller_name' => 'AdminProductController',
            'form' => $form->createView()
        ]);
    }
}
