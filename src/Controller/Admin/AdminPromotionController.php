<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Promotion;
use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPromotionController extends AbstractController
{
    private $promotionRepository;
    private $em;

    public function __construct(PromotionRepository $promotionRepository,EntityManagerInterface $em)
    {
        $this->promotionRepository = $promotionRepository;
        $this->em = $em;
    }
    /**
     * @Route("/admin/promotion/list", name="admin.promotion.list")
     */
    public function list()
    {
        $promotions = $this->promotionRepository->findAll();

        return $this->render('admin/promotion/list.html.twig', [
            'controller_name' => 'AdminPromotionController',
            'promos' => $promotions
        ]);
    }
    /**
     * @Route("/admin/promotion/editer/{id}", name="admin.promotion.editer")
     */
    public function editer(Request $request, Promotion $promotion)
    {
        $form = $this->createFormBuilder($promotion)
        ->add('dateBegin', DateType::class)
        ->add('dateEnd', DateType::class)
        ->add('Product', EntityType::class, [
            'class' => Product::class,
            'choice_label' => 'title',
            'disabled'   => true,])
        ->add('value', IntegerType::class,['label' => 'Pourcentage de la réduction'])
        ->add('numberProduct', IntegerType::class)
        ->add('Soumettre', SubmitType::class, ['label' => 'Mettre à jour'])
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {   
            $this->em->flush();
            return $this->redirectToRoute('admin.promotion.list');
        }

        return $this->render('admin/promotion/editer.html.twig', [
            'controller_name' => 'AdminPromotionController',
            'promotion' => $promotion,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/admin/promotion/add", name="admin.promotion.add")
     */
    public function add(Request $request)
    {   
        $promotion = new Promotion;

        $form = $this->createFormBuilder($promotion)
        ->add('dateBegin', DateType::class)
        ->add('dateEnd', DateType::class)
        ->add('Product', EntityType::class, [
            'class' => Product::class,
            'choice_label' => 'title',])
        ->add('value', IntegerType::class,['label' => 'Pourcentage de la réduction'])
        ->add('numberProduct', IntegerType::class)
        ->add('Ajouter', SubmitType::class, ['label' => 'Ajouter Promotion'])
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {   
            $this->em->persist($promotion);
            $this->em->flush();
            return $this->redirectToRoute('admin.promotion.list');
        }
        return $this->render('admin/promotion/add.html.twig', [
            'controller_name' => 'AdminPromotionController',
            'form' => $form->createView()
        ]);
    }
        /**
     * @Route("/admin/promotion/add/{id}", name="admin.promotion.addById")
     */
    public function addById(Product $product,Request $request)
    {
        $promotion = new Promotion;
        $promotion->setProduct($product);

        $form = $this->createFormBuilder($promotion)
        ->add('dateBegin', DateType::class)
        ->add('dateEnd', DateType::class)
        ->add('Product', EntityType::class, [
            'disabled'   => true,
            'class' => Product::class,
            'choice_label' => 'title',])
        ->add('value', IntegerType::class,['label' => 'Pourcentage de la réduction'])
        ->add('numberProduct', IntegerType::class)
        ->add('Ajouter', SubmitType::class, ['label' => 'Ajouter Promotion'])
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {   
            $this->em->persist($promotion);
            $this->em->flush();
            return $this->redirectToRoute('admin.promotion.list');
        }
        return $this->render('admin/promotion/add.html.twig', [
            'controller_name' => 'AdminPromotionController',
            'form' => $form->createView()
        ]);
    }
}
