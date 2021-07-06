<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AdminThemeController extends AbstractController
{
    private $em;
    private $repository;
    private $pictureRepository;

    public function __construct(ThemeRepository $repository,EntityManagerInterface $em,PictureRepository $pictureRepository)
    {
        $this->em = $em;
        $this->repository = $repository;    
        $this->pictureRepository = $pictureRepository;    
    }

    /**
     * @Route("/admin/theme", name="admin.theme.index")
     */
    public function index()
    {
        $themes = $this->repository->findAll();

        return $this->render('admin/theme/index.html.twig', [
            'themes' => $themes,
        ]);
    }
    /**
     * @Route("/admin/theme/add", name="admin.theme.add")
     */
    public function add(Request $request)
    {
        $theme = new Theme;

        $form = $this->createFormBuilder($theme)
        ->add('name', TextType::class,['label' => 'Titre du thème'])     
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
            
            if(!empty($image))
            {
                $img = $this->pictureRepository->registerPicture($image);
                $img->setTheme($theme);
                $theme->setPicture($img);
            }

            $this->em->persist($theme);
            $this->em->flush();
            return $this->redirectToRoute('admin.theme.index');
        }
        return $this->render('admin/theme/add.html.twig', [
            'form' => $form->createView()

        ]);
    }
    /**
     * @Route("/admin/theme/editer/{id}", name="admin.theme.editer")
     */
    public function editer(Theme $theme,Request $request)
    {
        $form = $this->createFormBuilder($theme)
        ->add('name', TextType::class,['label' => 'Titre du thème'])
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

            $picture = $theme->getPicture();

            if (!empty($image)){
                if(!empty($picture))
                {
                    $directory = $this->getParameter('images_directory');
                    unlink($directory.'/'.$picture->getName());
                    $this->em->remove($picture);
                    $this->em->flush();
                }

                $img = $this->pictureRepository->registerPicture($image);
                $img->setTheme($theme);
                $theme->setPicture($img);
            }

            $this->em->flush();
            return $this->redirectToRoute('admin.theme.index');
        }
        return $this->render('admin/theme/editer.html.twig', [
            'form' => $form->createView(),
            'theme' =>$theme
        ]);
    }
        /**
     * @Route("/admin/theme/favorite/{id}", name="admin.theme.favorite")
     */
    public function favorite(Theme $theme)
    {
        if($theme->getFavorites() == 1)
        {
            $theme->setFavorites(0);
        }
        else
        {
            $theme->setFavorites(1);
        }

        $this->em->flush();

        return $this->redirectToRoute('admin.theme.index');
    }
}
