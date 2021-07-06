<?php

namespace App\Controller;

use App\Entity\Page;
use App\Repository\GlobalParameterRepository;
use App\Repository\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    private $em;
    private $repository;
    private $globalParameterRepository;

    public function __construct(EntityManagerInterface $em,PageRepository $repository,GlobalParameterRepository $globalParameterRepository)
    {
        $this->em = $em;    
        $this->repository = $repository;
        $this->globalParameterRepository = $globalParameterRepository;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('page/index.html.twig',[
            ]);
    }
    /**
     * @Route("/page/footer", name="page.footer")
     */
    public function footer()
    {
        $pages = $this->repository->findBy(['online' => true]);
        $adresse = $this->globalParameterRepository->findOneByTitle("Adresse");
        $adresse = unserialize($adresse->getContent());
        
        return $this->render('partial/page/_footer.html.twig',[
            'pages' => $pages,
            'adresse' => $adresse
            ]);
    }
        /**
     * @Route("/page/{slug}-{id_page}", name="page.view", requirements={"slug":"[a-z0-9\-]*"})
     */
    public function view(Page $id_page)
    {
        return $this->render('page/view.html.twig',[
            'page' => $id_page
            ]);
    }
}
