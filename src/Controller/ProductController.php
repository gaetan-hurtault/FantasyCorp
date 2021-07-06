<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Editor;
use App\Entity\Languages;
use App\Entity\Product;
use App\Entity\SearchProduct;
use App\Entity\Theme;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
use App\Repository\ThemeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    /**
     *
     * @var ProductRepositary
     */
    private $repository;
    private $categoryRepository;
    private $promotionRepository;
    private $themeRepository;
    

    public function __construct(PromotionRepository  $promotionRepository,ProductRepository $repository, CategoryRepository $categoryRepository,ThemeRepository $themeRepository)
    {
        $this->repository = $repository;        
        $this->categoryRepository = $categoryRepository;
        $this->promotionRepository = $promotionRepository;
        $this->themeRepository = $themeRepository;
    }
    /**
     * @Route("/lastproduct", name="product.last")
     * @return Response
     */
    public function last()
    {
        $products = $this->repository->findProductWithPromo();

        return $this->render('partial/product/_lastproduct.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/category/{slug}-{id_category}", name="product.byCategory", requirements={"slug":"[a-z0-9\-]*"}, methods={"GET","HEAD"})
     * @return Response
     */
    public function byCategory($slug,$id_category,Request $request,PaginatorInterface $paginator)
    {

        $category = $this->categoryRepository->findOneById($id_category);  
        
        //Conception du formulaire de recherche
        $searchProduct = new SearchProduct;

        if (isset($_GET['tag']))
        {
            $searchProduct->setTag($_GET['tag']);
        }

        $searchProduct->addCategories($category);

        $form = $this->createFormBuilder($searchProduct,['method' => 'GET',])
        ->add('search', TextType::class,[
            'required' => false,
            'attr' =>[
                'placeholder' => "Que recherchez-vous ?"
            ]
        ])
        ->add('categories', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'label' => ' ',
            'required' => false,
            'multiple' => true,
                'attr' =>['
                class' => 'd-none']
        ])
        ->add('tri', ChoiceType::class, [
            'label' => 'Tri :',
            'choices'  => [
                '           ' => null,
                'Du - au + cher ' => 'asc',
                'Du + au - cher' => 'desc',
                'Les Mieux Notés' => 'bestRating',
                'Les Moins Bien Notés' => 'lowRating',
                'ordre Alphabétique' => 'alpha',
            ],])
        ->add('editor', EntityType::class, [
            'label' => 'Editeur',
            'class' => Editor::class,
            'choices' => $category->getEditors(),
            'choice_label' => 'name',
            'required' => false,
        ])
        ->add('themes', EntityType::class, [
            'label' => 'Thèmes',
            'class' => Theme::class,
            'choice_label' => 'name',
            'required' => false,
            'multiple' => true,
        ])
        ->add('languages', EntityType::class, [
            'label' => 'Langues',
            'class' => Languages::class,
            'choice_label' => 'name',
            'required' => false,
            'multiple' => true,
        ])
        ->add('prixMin', NumberType::class,[
            'required' => false,
        ])
        ->add('prixMax', NumberType::class,[
            'required' => false,
        ])
        ->add('condition', ChoiceType::class, [
            'label' => 'Condition',
            'required' => false,
            'choices'  => [
                'Neuf' => "neuf",
                "Occasion" => "occasion",
            ],
        ])
        ->add('rechercher', SubmitType::class, ['label' => 'Rechercher'])
        ->getForm();

        if ($category->getName() != "Goodies" 
        && $category->getName() != "Autres"
        && (empty($category->getCategoryParent()) 
        || ($category->getCategoryParent()->getName() != "Goodies" 
        && $category->getCategoryParent()->getName() != "Autres")))
        {
            $form->add('ageMin', NumberType::class,[
                'label' => 'Âge Minimum',
                'required' => false,
            ]);
        }

        if ($category->getName() == "Jeux de Société"){
            $form->add('timeMin', ChoiceType::class, [
                'required' => false,
                'choices'  => [
                    'Moins de 30 Minutes' => 0,
                    '30 Minutes' => 30,
                    '1 Heure' => 60,
                    '2 Heures' => 120,
                    'Plus de 3 Heures' => 180
                ],])
                ->add('timeMax', ChoiceType::class, [
                    'required' => false,
                    'choices'  => [
                    'Moins de 30 Minutes' => 0,
                    '30 Minutes' => 30,
                    '1 Heure' => 60,
                    '2 Heures' => 120,
                    'Plus de 3 Heures' => 180
                ],])
                ->add('nbrPlayerMin', NumberType::class,[
                    'required' => false,
                ])
                ->add('nbrPlayerMax', NumberType::class,[
                    'required' => false,
                ]);
        }

        $form->handleRequest($request);
        
        /*if ($form->isSubmitted() && $form->isValid())
        {          
        }*/

        $products = $paginator->paginate(
            $this->repository->findProductWithPromo($searchProduct), // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            2 // Nombre de résultats par page
        );

        return $this->render('product/bycategory.html.twig', [
            'products' => $products,
            'category' => $category,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/product/ajax_search", name="product.search")
     * @return Response
     */
    public function search(Request $request)
    {
        $search = new SearchProduct;
        $search->setSearch($request->request->get('search'));
        
        $donnees = $this->repository->findProductWithPromo($search,true);
        
        if (!empty($donnees))
        {
            $view = $this->renderView('partial/product/_searchproduct.html.twig',[
                'products' => $donnees,
                ]);
        }
        else
        {
            $view = 0;
        }

        $response = new Response(json_encode(array(
            'view' => $view
        )));

        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
    /**
     * @Route("/product/searchproduct", name="product.search.page", methods={"GET","HEAD"})
     * @return Response
     */
    public function searchProduct(Request $request,PaginatorInterface $paginator)
    {
        //Conception du formulaire de recherche
        $searchProduct = new SearchProduct;

        if (isset($_GET['searchValue']))
        {
            $searchProduct->setSearch($_GET['searchValue']);
        }
        
        if (isset($_GET['tag']))
        {
            $searchProduct->setTag($_GET['tag']);
        }

        if (isset($_GET['theme']))
        {
            $searchProduct->addThemes(
                $this->themeRepository->findOneById($_GET['theme']));
        }
        
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
        ->add('tri', ChoiceType::class, [
            'label' => 'Tri :',
            'choices'  => [
                '           ' => null,
                'Du - au + cher ' => 'asc',
                'Du + au - cher' => 'desc',
                'ordre Alphabétique' => 'alpha',
            ],])
        ->add('editor', EntityType::class, [
            'label' => 'Editeur',
            'class' => Editor::class,
            'choice_label' => 'name',
            'required' => false,
        ])
        ->add('themes', EntityType::class, [
            'label' => 'Thèmes',
            'class' => Theme::class,
            'choice_label' => 'name',
            'mapped' =>false,
            'required' => false,
            'multiple' => true,
            'data' => $searchProduct->getThemes()
        ])
        ->add('languages', EntityType::class, [
            'label' => 'Langues',
            'class' => Languages::class,
            'choice_label' => 'name',
            'required' => false,
            'multiple' => true,
        ])
        ->add('prixMin', NumberType::class,[
            'required' => false,
        ])
        ->add('prixMax', NumberType::class,[
            'required' => false,
        ])
        ->add('condition', ChoiceType::class, [
            'label' => 'Condition',
            'choices'  => [
                'Neuf' => "neuf",
                "Occasion" => "occasion",
            ],
        ])
        ->add('rechercher', SubmitType::class, ['label' => 'Rechercher'])
        ->getForm();

        $form->handleRequest($request);

        $products = $paginator->paginate(
            $this->repository->findProductWithPromo($searchProduct), // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            20 // Nombre de résultats par page
        );

        return $this->render('product/bycategory.html.twig', [
            'products' => $products,
            'searchProduct' => $searchProduct,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/product/{slug}-{id_product}", name="product.information", requirements={"slug":"[a-z0-9\-]*"})
     * @return Response
     */
    public function information($slug,$id_product)
    {
        $product = $this->repository->findOneById($id_product);
        $promo = $this->promotionRepository->pricePromotionByProduct($product);

        return $this->render('product/information.html.twig', [
            'product' => $product,
            'category' => $slug,
            'promo' => $promo
        ]);
    }
    /**
     * @Route("/product/{idCategory}", name="product.menu")
     * @return Response
     */
    public function menuProduct($idCategory)
    {
        $product = $this->repository->findOneBy(['category' => $idCategory, 'online' => true],['dateAdd' => "DESC"]);

        $promo = $this->promotionRepository->pricePromotionByProduct($product);

        return $this->render('partial/product/_cardproductX.html.twig', [
            'product' => $product,
            'promo' => $promo
        ]);
    }
}