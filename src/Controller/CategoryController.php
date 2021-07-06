<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    /**
     *
     * @var CategoryRepositary
     */
    private $repository;
    private $em;

    public function __construct(CategoryRepository $repository, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->repository = $repository;        
    }
    /**
     * @Route("/menu", name="menu")
     */
    public function menu() : Response
    {
        $categories = $this->repository->findBy(['categoryParent' => null]);
        return $this->render('partial/category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }
    /**
     * @Route("/menumobile", name="menu.mobile")
     */
    public function menuMobile() : Response
    {
        $categories = $this->repository->findBy(['categoryParent' => null]);
        return $this->render('partial/category/_menumobile.html.twig', [
            'categories' => $categories
        ]);
    }
    /**
     * @Route("/admin/category", name="admin.category")
     */
    public function addCategory(Request $request) : Response
    {
        $category = new Category;

        $form = $this->createFormBuilder($category)
        ->add('name', TextType::class)
        ->add('categoryParent', EntityType::class, [
            'class' => Category::class,
            'choice_label' => 'name',
            'required' => false,
            'attr' => ['class' => null]])
        ->add('Ajouter', SubmitType::class, ['label' => 'Ajouter'])
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($category);
            $this->em->flush();
            return $this->redirectToRoute('admin.product.catalogue');
        }

        return $this->render('form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
