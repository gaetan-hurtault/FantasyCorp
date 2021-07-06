<?php

namespace App\Controller;

use App\Repository\BasketRepository;
use App\Repository\CommandLineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommandLineController extends AbstractController
{
    private $em;
    private $commandLineRepository;
    private $basketRepository;

    public function __construct(CommandLineRepository $commandLineRepository,EntityManagerInterface $em,BasketRepository $basketRepository)
    {
        $this->em = $em;
        $this->commandLineRepository = $commandLineRepository;
        $this->basketRepository = $basketRepository;
    }
    /**
     * @Route("/updateCommandLine", name="commandLine.update")
     */
    public function update()
    {
        if (isset($_POST['idCommandLine']))
        {
            $commandLine = $this->commandLineRepository->findOneById($_POST['idCommandLine']);
            $commandLine->setQuantity($_POST['quantity']);
            $this->em->flush();
        }

        return $this->redirectToRoute('basket.show');
    }
    /**
     * @Route("/deleteCommandLine", name="commandLine.delete")
     */
    public function delete()
    {
        $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);

        if (isset($_POST['idCommandLine']))
        {
            $commandLine = $this->commandLineRepository->findOneById($_POST['idCommandLine']);
            $this->em->remove($commandLine);
            $this->em->flush();
        }

        $this->basketRepository->isEmptyBasket($basket);

        return $this->redirectToRoute('basket.show');
    }
}
