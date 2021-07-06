<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ThemeRepository;

class ThemeController extends AbstractController
{
    private $repository;
    
    public function __construct(ThemeRepository $repository)
    {
        $this->repository = $repository;    
    }
    /**
     * @Route("/theme/favorite", name="theme.favorite")
     */
    public function favorite()
    {
        $themes = $this->repository->findBy(['favorites' => true],null,6);
        
        return $this->render('partial/theme/_favorite.html.twig', [
            'themes' => $themes
        ]);
    }
}
