<?php

namespace App\Controller\Admin;

use App\Entity\GlobalParameter;
use App\Repository\GlobalParameterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AdminGlobalParameterController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em,GlobalParameterRepository $repository)
    {
        $this->em = $em;
        $this->repository = $repository;
    }
    /**
     * @Route("/admin/globalparameter", name="admin.globalparameter.index")
     */
    public function index()
    {
        
        $parametres = $this->repository->findAll();

        return $this->render('admin/globalparameter/index.html.twig', [
            'parametres' => $parametres,
        ]);
    }
    /**
     * @Route("/admin/globalparameter/editer/{id}", name="admin.globalparameter.editer")
     */
    public function editer(GlobalParameter $parametre,Request $request)
    {
        
        $content = $parametre->getContent();

        $form = $this->createFormBuilder();

        switch ($content[0]) {
            case 'a':
                $content = unserialize($content);
                foreach ($content as $key => $value) {
                    $form->add($key , TextType::class,
                    ['label' => $key."g",
                    'data' => $value]);
                }
                break;
            case 's':
                $content = unserialize($content);
                $form->add('description', CKEditorType::class,
                ['label' => 'Contenu du paramètre',
                'data' => $content]);  
                break;
            
            default:
                break;
        }

        $form = $form->add('Editer', SubmitType::class, ['label' => 'Editer'])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {   
            $data = $form->getData();
            $content = serialize($content);
            switch ($content[0]) {
                case 'a':
                    $content = unserialize($content);
                    foreach ($data as $key => $value) {
                        $content[$key] = $data[$key];
                    }
                    $parametre->setContent(serialize($content)); 
                    break;
                case 's':
                  $parametre->setContent(serialize($data['description'])); 
                    break;
                
                default:
                    # code...
                    break;
            }

            $this->em->flush();
            return $this->redirectToRoute('admin.globalparameter.index');
        }

        return $this->render('admin/globalparameter/editer.html.twig', [
            'form' => $form->createView(),
            'title' =>$parametre->getTitle(),
        ]);
    }
    /**
     * @Route("/admin/globalparameter/register", name="admin.globalparameter.register")
     */
    public function register()
    {
        $globalParameter = new GlobalParameter;

        $globalParameter->setTitle("Colissimo France DOM-TOM2");

        $content = array(
            "500" => 11.65,
            "1000" => 17.45,
            "2000" => 30.60,
            "5000" => 51.50,
            "10000" => 100.50,
            "15000" => 100.50,
            "30000" => 245.50
        );

        $content = serialize($content);

        $globalParameter->setContent($content);
        
        $this->em->persist($globalParameter);
        $this->em->flush();

        return $this->redirectToRoute('homepage');
    }
}
