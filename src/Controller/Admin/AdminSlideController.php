<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Entity\Product;
use App\Entity\Slide;
use App\Entity\Theme;
use App\Repository\PictureRepository;
use App\Repository\SlideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSlideController extends AbstractController
{
    private $em;
    private $repository;
    private $pictureRepository;

    public function __construct(EntityManagerInterface $em,SlideRepository $repository,PictureRepository $pictureRepository)
    {
        $this->em = $em;    
        $this->repository = $repository;
        $this->pictureRepository = $pictureRepository;
    }
    /**
     * @Route("/admin/slide", name="admin.slide.index")
     */
    public function index()
    {
        $slides = $this->repository->findAll();
        return $this->render('admin/slide/index.html.twig', [
            'slides' => $slides
        ]);
    }
     /**
     * @Route("/admin/slide/add", name="admin.slide.add")
     */
    public function add(Request $request)
    {
        $slide = new Slide;

        $form = $this->createFormBuilder($slide)
        ->add('name', TextType::class,['label' => 'Titre du slide'])
        ->add('theme', EntityType::class,[
            'class' => Theme::class,
            'choice_label' => 'name',
            'required' => false
        ] )
        ->add('product', EntityType::class,[
            'class' => Product::class,
            'choice_label' => 'title',
            'required' => false
        ] )
        ->add('page', EntityType::class,[
            'class' => Page::class,
            'choice_label' => 'title',
            'required' => false
        ])
        ->add('picture', FileType::class,[
            'label' => false,
            'multiple' => false,
            'mapped' => false,
            'required' => false
        ])
        ->add('Ajouter', SubmitType::class, ['label' => 'Valider'])
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {  
            $image= $form->get('picture')->getData();
            $img = $this->pictureRepository->registerPicture($image);
            $slide->setPicture($img);

            $this->em->persist($slide);
            $this->em->flush();
            return $this->redirectToRoute('admin.slide.index');
        }
        return $this->render('admin/slide/add.html.twig', [
            'form' => $form->createView()

        ]);
    }
    /**
     * @Route("/admin/slide/delete/{id}", name="admin.slide.delete")
     */
    public function delete(Slide $slide)
    {
        $img = $slide->getPicture();

        $directory = $this->getParameter('images_directory');
        unlink($directory.'/'.$img->getName());

        $this->em->remove($slide);
        $this->em->flush();
        return $this->redirectToRoute('admin.page.index');
    }
    /**
     * @Route("/admin/slide/editer/{id}", name="admin.slide.editer")
     */
    public function editer(Slide $slide,Request $request)
    {
        $form = $this->createFormBuilder($slide)
        ->add('name', TextType::class,['label' => 'Titre du slide'])
        ->add('picture', FileType::class,[
            'label' => false,
            'multiple' => false,
            'mapped' => false,
            'required' => false
        ])
        ->add('Ajouter', SubmitType::class, ['label' => 'Valider'])
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {   
            $image = $form->get('picture')->getData();

            $picture = $slide->getPicture();

            if (!empty($image)){
                if(!empty($picture))
                {
                    $directory = $this->getParameter('images_directory');
                    unlink($directory.'/'.$picture->getName());
                    $this->em->remove($picture);
                    $this->em->flush();
                }

                $img = $this->pictureRepository->registerPicture($image);
                $slide->setPicture($img);
            }

            $this->em->flush();
            return $this->redirectToRoute('admin.slide.index');
        }
        return $this->render('admin/slide/editer.html.twig', [
            'form' => $form->createView(),
            'slide' => $slide
        ]);
    }
}
