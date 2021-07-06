<?php

namespace App\Controller;

use App\Repository\SlideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SlideController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em,SlideRepository $repository)
    {
        $this->em = $em;    
        $this->repository = $repository;
    }
    /**
     * @Route("/slide/render", name="slide.render")
     */
    public function renderSlide()
    {
        $slides = $this->repository->findAll();
        return $this->render('partial/slide/_diapo.html.twig', [
            'slides' => $slides ,
        ]);
    }
}
