<?php

namespace App\Controller;

use App\Repository\PictureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PictureController extends AbstractController
{
    private $repository;
    private $em;

    public function __construct(PictureRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
        
    }

    /**
     * @Route("/admin/delete", name="admin.picture.delete")
     */
    public function deletaAjax(Request $request)
    {
        $idPicture = $request->request->get('idPicture');
        
        $picture = $this->repository->findOneById($idPicture);

        $directory = $this->getParameter('images_directory');
        unlink($directory.'/'.$picture->getName());

        $this->em->remove($picture);
        $this->em->flush();

        $response = new Response(json_encode(array(
            'success' => true
        )));

        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
}
