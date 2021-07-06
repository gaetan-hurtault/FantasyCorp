<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends AbstractController
{
    private $productRepository;
    private $userRepository;
    private $em;
    private $repository;

    public function __construct(ProductRepository $productRepository,UserRepository $userRepository, EntityManagerInterface $em,CommentRepository $repository)
    {
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
        $this->repository = $repository;
        $this->em = $em;
    }
    /**
     * @Route("/profil/comment/add/{product}", name="comment.add")
     */
    public function add(Request $request,Product $product)
    {
        $comment = $this->repository->findOneBy(
            ['user' => $this->getUser(),'product' => $product]);

        if($comment != null)
        {
            return $this->redirectToRoute('comment.editer',[
                'product' => $product->getId()]);
        }

        $comment = new Comment;

        $form = $this->createFormBuilder($comment)
        ->add('title', TextType::class)
        ->add('recommended',  CheckboxType::class, [
            'label'    => 'Recommandez-vous ce produit ?',
            'required' => false,
        ])
        ->add('description', TextareaType::class)
        ->add('Valider', SubmitType::class, ['label' => 'Valider'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
        $comment->setDateCreated(new \DateTime('@'.strtotime('now')));
        $comment->setUser(
            $this->userRepository->findOneById($this->getUser()));

        $product->addComment($comment);
        $this->em->flush();
        $product = $this->repository->calculProductRating($product);
        $this->em->flush();
        return $this->redirectToRoute('product.information', ['slug' => $product->getSlug(),'id_product' => $product->getId()]);
    }  

        return $this->render('comment/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/profil/comment/editer/{product}", name="comment.editer")
     */
    public function editer(Product $product,Request $request)
    {
        $comment = $this->repository->findOneBy(
            ['user' => $this->getUser(),'product' => $product]);
        $form = $this->createFormBuilder($comment)
        ->add('title', TextType::class)
        ->add('recommended',  CheckboxType::class, [
            'label'    => 'Recommandez-vous ce produit ?',
            'required' => false,
        ])
        ->add('description', TextareaType::class)
        ->add('Editer', SubmitType::class, ['label' => 'Editer'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
        $this->em->flush();
        $product = $this->repository->calculProductRating($product);
        $this->em->flush();
        return $this->redirectToRoute('product.information', ['slug' => $product->getSlug(),'id_product' => $product->getId()]);
    }  

        return $this->render('comment/editer.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
