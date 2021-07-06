<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Editor;
use App\Repository\EditorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminEditorController extends AbstractController
{
    private $em;
    private $repository;

    public function __construct(EntityManagerInterface $em,EditorRepository $repository)
    {
        $this->em = $em;    
        $this->repository = $repository;
    }
    /**
     * @Route("/admin/editor/index", name="admin.editor.index")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $donnees = $this->repository->findAll();

        $editor = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            5 // Nombre de résultats par page
        );

        return $this->render('admin/editor/index.html.twig', [
            'editors' => $editor
        ]);
    }
    /**
     * @Route("/admin/editor/add", name="admin.editor.add")
     */
    public function add(Request $request)
    {
        $editor = new Editor;

        $form = $this->createFormBuilder($editor)
        ->add('name', TextType::class,[
            'label' => 'Nom :'
        ])
        ->add('categories', EntityType::class, [
            'label' => 'Catégories :',
            'class' => Category::class,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => false])   
            ->add('Ajouter', SubmitType::class, ['label' => 'Ajouter'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->em->persist($editor);
            $this->em->flush();
            return $this->redirectToRoute('admin.editor.index');
        }  
        
        return $this->render('admin/editor/add.html.twig', [
            'form' => $form->createView()
        ]);

    }
        /**
     * @Route("/admin/editor/editer/{id}", name="admin.editor.editer")
     */
    public function editer(Editor $editor ,Request $request)
    {

        $form = $this->createFormBuilder($editor)
        ->add('name', TextType::class,[
            'label' => 'Nom :'
        ])
        ->add('categories', EntityType::class, [
            'label' => 'Catégories :',
            'class' => Category::class,
            'choice_label' => 'name',
            'multiple' => true,
            'expanded' => false])   
        ->add('Editer', SubmitType::class, ['label' => 'Editer'])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
    {
        $this->em->flush();
        return $this->redirectToRoute('admin.editor.index');
    }  

        return $this->render('admin/editor/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
