<?php

namespace App\Controller\Admin;

use App\Entity\Adress;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use PayPal\Api\RedirectUrls;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em,UserRepository $repository)
    {
        $this->em = $em;    
        $this->repository = $repository;
    }
    /**
     * @Route("/admin/user", name="admin.user.index")
     */
    public function index(Request $request ,PaginatorInterface $paginator)
    {
        $users = $paginator->paginate(
            $this->repository->findBy(['role' => 0]), // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            20 // Nombre de résultats par page
        );

        return $this->render('admin/user/index.html.twig', [
            'users' =>$users
        ]);
    }
    /**
     * @Route("/admin/user/view/{user}", name="admin.user.view")
     */
    public function view(User $user)
    {
        return $this->render('admin/user/view.html.twig', [
            'user' =>$user
        ]);
    }

    /**
     * @Route("/admin/user/addadress/{user}", name="admin.user.addadress")
     */
    public function addAdress(Request $request,User $user)
    {

        $adress =  new Adress;

        $form = $this->createFormBuilder($adress)
        ->add('firstName',TextType::class,[
            'label' => 'Prénom :'
        ])
        ->add('lastName',TextType::class,[
            'label' => 'Nom :'
        ])
        ->add('adress',TextareaType::class,[
            'label' => 'Adresse :'
        ])
        ->add('city',TextType::class,[
            'label' => 'Ville :'
        ])
        ->add('codePostal',TextType::class,[
            'label' => 'Code Postal :'
        ])
        ->add('country',TextType::class,[
            'label' => 'Pays :'
        ])
        ->add('phoneNumber',TextType::class,[
            'label' => 'Numéro de Téléphone :'
        ])
        ->add('Valider', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $adress->setType(1);
                $user->addAdress($adress);
                
                $this->em->flush();

                return $this->redirectToRoute('admin.user.index');
            }

        return $this->render('admin/user/addadress.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
