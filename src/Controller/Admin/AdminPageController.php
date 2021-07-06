<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use App\Repository\PageRepository;
use App\Repository\SlideRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminPageController extends AbstractController
{
    private $em;
    private $repository;
    private $slideRepository;

    public function __construct(EntityManagerInterface $em,PageRepository $repository,SlideRepository $slideRepository)
    {
        $this->em = $em;    
        $this->repository = $repository;
        $this->slideRepository = $slideRepository;
        ;
    }
    /**
     * @Route("/admin", name="admin.index")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }
    /**
     * @Route("/admin/page", name="admin.page.index")
    */
    public function list()
    {
        $pages = $this->repository->findAll();
        return $this->render('admin/page/index.html.twig', [
            'pages' => $pages
            ]);
    }
        /**
     * @Route("/admin/page/add", name="admin.page.add")
     */
    public function add(Request $request)
    {
        $page = new Page;

        $form = $this->createFormBuilder($page)
            ->add('title', TextType::class,
            ['label' => 'Titre de la Page'])
            ->add('description', CKEditorType::class,
            ['label' => 'Contenu de la Page'])   
            ->add('Ajouter', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $page->setOnline(true);
            $this->em->persist($page);
            $this->em->flush();
            return $this->redirectToRoute('admin.page.index');
        }  
        
        return $this->render('admin/page/add.html.twig', [
            'form' => $form->createView()
        ]);

    }
        /**
     * @Route("/admin/page/editer/{id}", name="admin.page.editer")
     */
    public function editer(Page $page ,Request $request)
    {

        $form = $this->createFormBuilder($page)
            ->add('title', TextType::class,
            ['label' => 'Titre de la Page'])
            ->add('description', CKEditorType::class,
            ['label' => 'Contenu de la Page'])  
            ->add('Editer', SubmitType::class, ['label' => 'Editer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->flush();
            return $this->redirectToRoute('admin.page.index');
        }  

        return $this->render('admin/page/editer.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/admin/page/delete/{id}", name="admin.page.delete")
     */
    public function delete(Page $page)
    {
        $slide = $this->slideRepository->findOneByPage($page);

        if($slide != null)
        {
            $img = $slide->getPicture();

            $directory = $this->getParameter('images_directory');
            unlink($directory.'/'.$img->getName());

            $this->em->remove($slide);
        }

        $this->em->remove($page);
        $this->em->flush();
        return $this->redirectToRoute('admin.page.index');
    }
        /**
     * @Route("/admin/page/online/{id}", name="admin.page.online")
     */
    public function online(Page $page)
    {
        if($page->getOnline() == 1)
        {
            $page->setOnline(0);
        }
        else
        {
            $page->setOnline(1);
        }

        $this->em->flush();

        return $this->redirectToRoute('admin.page.index');
    }
}
