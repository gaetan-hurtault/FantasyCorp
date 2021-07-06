<?php

namespace App\Controller\Admin;

use App\Entity\Basket;
use App\Repository\BasketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBasketController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em,BasketRepository $repository)
    {
        $this->em = $em;    
        $this->repository = $repository;
    }
    /**
     * @Route("/admin/basket", name="admin.basket.index")
     */
    public function index(Request $request,PaginatorInterface $paginator)
    {

        $baskets = $paginator->paginate(
            $this->repository->findAll(), // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            20 // Nombre de résultats par page
        );

        return $this->render('admin/basket/index.html.twig', [
        'baskets' => $baskets    
        ]);
    }
        /**
     * @Route("/admin/basket/view/{basket}", name="admin.basket.view")
     */
    public function view(Basket $basket)
    {
        return $this->render('admin/basket/view.html.twig', [
            'basket' => $basket    
            ]);
    }
}
