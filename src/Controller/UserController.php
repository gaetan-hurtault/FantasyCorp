<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\BlueCard;
use App\Entity\Register;
use App\Entity\User;
use App\Form\RegisterType;
use App\Form\UserType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     *
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     *
     * @var UserPasswordEncoderInterface;
     */
    private $passwordEncoder;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository,EntityManagerInterface $em,UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }
    /**
     * @Route("/register", name="user.register")
     */
    public function register(Request $request)
    {
        if($this->getUser())
        {
            return $this->redirectToRoute('homepage');
        }

        $register = new Register;
        $form = $this->createForm(RegisterType::class,$register);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $error = "";
            if($register->getPassword() != $register->getPasswordVerif())
            {
                $error = "mots de passe différents";

                return $this->render('user/register.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }

            $adress = new Adress;
            $adress->setFirstName($register->getFirstName())
                    ->setLastName($register->getLastName())
                    ->setAdress($register->getAdress())
                    ->setCity($register->getCity())
                    ->setCodePostal($register->getCodePostal())
                    ->setPhoneNumber($register->getPhoneNumber())
                    ->setCountry($register->getCountry())
                    ->setType(0);

            $user = new User;
            $user->setFirstName($register->getFirstName())
                 ->setLastName($register->getLastName())
                 ->setMail($register->getMail())
                 ->setPassword($this->passwordEncoder->encodePassword(
                        $user,
                        $register->getPassword()))
                 ->setAdressBill($adress)
                 ->setRole(0);

            $adress->setUser($user);

            $this->em->persist($user);
            $this->em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/profile/index", name="user.profile")
     */
    public function index()
    {

        $user = $this->getUser();

        return $this->render('user/profile.html.twig', [
            'user' => $user
        ]); 
    }
    /**
     * @Route("/profile/update", name="user.profile.update")
     */
    public function update(Request $request)
    {

        $user =  $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('mail', EmailType::class)
            ->add('Valider', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $this->em->flush();

                return $this->render('user/profile.html.twig', [
                    'user' => $user,
                    'success' => "Informations mises à jours !"
                ]); 
            }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/profile/list/adress", name="user.profile.listadress")
     */
    public function listAdress()
    {
        $user = $this->getUser();

        $adress = $user->getAdress();

        return $this->render('user/listadress.html.twig', [
            'adress' => $adress
        ]);
    }
    /**
     * @Route("/profile/list/adress/{idAdress}", name="user.profile.editeradress")
     */
    public function editerAdress(Request $request,Adress $idAdress)
    {
        $user = $this->getUser();
        
        if($idAdress->getUser() != $user)
        {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createFormBuilder($idAdress)
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
                
                $this->em->flush();

                return $this->render('user/listadress.html.twig', [
                    'adress' => $user->getAdress(),
                    'success' => "Adresse modifiée !"
                ]); 
            }

        return $this->render('user/editeradress.html.twig', [
            'form' => $form->createView(),
        ]);

    }
        /**
     * @Route("/profile/delete/adress/{adress}", name="user.profile.deleteadress")
     */
    public function deleteAdress(Adress $adress)
    {
        $user = $this->getUser();
        
        if($adress->getUser() != $user || $adress->getId() == $user->getAdressBill()->getId())
        {
            return $this->redirectToRoute('homepage');
        }

        $user->removeAdress($adress);
        $this->em->flush();

        return $this->redirectToRoute('user.profile.listadress');
    }
            /**
     * @Route("/profile/bill/adress/{adress}", name="user.profile.billadress")
     */
    public function billAdress(Adress $adress)
    {
        $user = $this->getUser();
        
        if($adress->getUser() != $user || $adress->getId() == $user->getAdressBill()->getId())
        {
            return $this->redirectToRoute('homepage');
        }

        $user->setAdressBill($adress);
        $this->em->flush();

        return $this->redirectToRoute('user.profile.listadress');
    }
    /**
     * @Route("/profile/add/adress", name="user.profile.addadress")
     */
    public function addAdress(Request $request)
    {

        $adress =  new Adress;

        $form = $this->createFormBuilder($adress)
        ->add('firstName',TextType::class,[
            'label' => 'Prénom :',
            'required' => true
        ])
        ->add('lastName',TextType::class,[
            'label' => 'Nom :',
            'required' => true
        ])
        ->add('adress',TextareaType::class,[
            'label' => 'Adresse :',
            'required' => true
        ])
        ->add('city',TextType::class,[
            'label' => 'Ville :',
            'required' => true
        ])
        ->add('codePostal',TextType::class,[
            'label' => 'Code Postal :',
            'required' => true
        ])
        ->add('country',TextType::class,[
            'label' => 'Pays :',
            'required' => true
        ])
        ->add('phoneNumber',TextType::class,[
            'label' => 'Numéro de Téléphone :',
            'required' => true
        ])
        ->add('Valider', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $adress->setType(1);
                $user = $this->getUser();
                $user->addAdress($adress);
                
                $this->em->flush();

                return $this->render('user/listadress.html.twig', [
                    'adress' => $user->getAdress(),
                    'success' => "Adresse ajoutée !"
                ]); 
            }

        return $this->render('user/addadress.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/profile/updatepassword", name="user.profile.password")
     */
    public function updatePassword(Request $request)
    {

        $user =  $this->getUser();

        $form = $this->createFormBuilder()
            ->add('oldMdp', PasswordType::class, ['label' => 'Ancien Mot de Passe :'])
            ->add('newMdp', PasswordType::class, ['label' => 'Nouveau Mot de Passe :'])
            ->add('verifMdp', PasswordType::class, ['label' => 'Vérification :'])
            ->add('Valider', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $data = $form->getData() ;

                if(!$this->passwordEncoder->isPasswordValid($user, $data['oldMdp']))
                {
                    $error = "Ancien Mot de passe Invalide";
                }
                else if($data['verifMdp'] != $data['newMdp'])
                {
                    $error = "Nouveau Mot de passe et Vérification différents";
                }

                if(isset($error))
                {
                    return $this->render('user/update.html.twig', [
                        'form' => $form->createView(),
                        'error' => $error
                        ]);
                }
                else
                {
                    $user->setPassword($this->passwordEncoder->encodePassword($user,$data['newMdp']));
                    $this->em->flush();

                    return $this->render('user/profile.html.twig', [
                        'user' => $user,
                        'success' => "Mot de passe mis à jour !"
                    ]); 
                }
            }

        return $this->render('user/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
        /**
     * @Route("/profile/baskets", name="user.profile.baskets")
     */
    public function showBaskets()
    {
        $user = $this->getUser();

        return $this->render('user/baskets.html.twig', [
            'user' => $user
        ]);    
    }
    /**
     * @Route("/profile/list/card", name="user.profile.listcard")
     */
    public function listCard()
    {
        $user = $this->getUser();

        $cards = $user->getBlueCards();

        return $this->render('user/listcard.html.twig', [
            'cards' => $cards
        ]);
    }
    /**
     * @Route("/profile/list/card/{card}", name="user.profile.editercard")
     */
    public function editerCard(Request $request,BlueCard $card)
    {
        $user = $this->getUser();
        
        if($card->getUser() != $user)
        {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createFormBuilder($card)
        ->add('firstName',TextType::class,[
            'label' => 'Prénom :',
            'required' => true
        ])
        ->add('lastName',TextType::class,[
            'label' => 'Nom :',
            'required' => true
        ])
        ->add('cardNumber',TextType::class,[
            'label' => 'Numéro de Carte :',
            'required' => true
        ])
        ->add('dateExpiration',DateType::class,[
            'label' => 'Date Expiration :',
            'required' => true,
        ])
        ->add('type',ChoiceType::class,[
            'label' => 'Type de Carte :',
            'choices' => [
                'Mastercard' => 1,
                'Visa' => 2,
            ],
            'required' => true
        ])
        ->add('Valider', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                
                $this->em->flush();

                return $this->render('user/listcard.html.twig', [
                    'cards' => $user->getBlueCards(),
                    'success' => "Carte de Paiement modifiée !"
                ]); 
            }

        return $this->render('user/editcard.html.twig', [
            'form' => $form->createView(),
        ]);

    }
        /**
     * @Route("/profile/delete/dard/{card}", name="user.profile.deletecard")
     */
    public function deleteCard(BlueCard $card)
    {
        $user = $this->getUser();
        
        if($card->getUser() != $user)
        {
            return $this->redirectToRoute('homepage');
        }

        $user->removeBlueCard($card);
        $this->em->flush();

        return $this->redirectToRoute('user.profile.listcard');
    }
    /**
     * @Route("/profile/add/card", name="user.profile.addcard")
     */
    public function addCard(Request $request)
    {

        $card = new BlueCard;
        $form = $this->createFormBuilder($card)
        ->add('firstName',TextType::class,[
            'label' => 'Prénom :',
            'required' => true
        ])
        ->add('lastName',TextType::class,[
            'label' => 'Nom :',
            'required' => true
        ])
        ->add('cardNumber',TextType::class,[
            'label' => 'Numéro de Carte :',
            'required' => true
        ])
        ->add('dateExpiration',DateType::class,[
            'label' => 'Date Expiration :',
            'required' => true,
        ])
        ->add('type',ChoiceType::class,[
            'label' => 'Type de Carte :',
            'choices' => [
                'Mastercard' => 1,
                'Visa' => 2,
            ],
            'required' => true
        ])
        ->add('Valider', SubmitType::class, ['label' => 'Valider'])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $user = $this->getUser();
                $user->addBlueCard($card);
                
                $this->em->flush();

                return $this->render('user/listcard.html.twig', [
                    'cards' => $user->getBlueCards(),
                    'success' => "Carte de Paiement ajoutée !"
                ]); 
            }

        return $this->render('user/addcard.html.twig', [
            'form' => $form->createView()
        ]);
    }
        /**
     * @Route("/profile/card/add/ajax", name="user.cardadd.ajax")
     */
    public function addCardAjax(Request $request)
    {
        if(!isset($_SESSION))
        {
            session_start();
        }

        $user = $this->getUser();

        $card = new BlueCard;

        $month = $request->request->get('dateCard');
        $date = new DateTime();
        $date->setDate(intval(
                        substr($month,0,4)
                    ),intval(
                        substr($month,5,2)
                    ),00);

        $card->setFirstName($request->request->get('firstName'))
                ->setLastName($request->request->get('lastName'))
                ->setCardNumber($request->request->get('cardNumber'))
                ->setType($request->request->get('typeCard'))
                ->setDateExpiration($date);

        $user->addBlueCard($card);
        $this->em->flush();

        $cards = $user->getBlueCards();

        $response = new Response(json_encode(array(
            'idCard' => ($cards[count($cards)-1])->getId()
        )));

        $response->headers->set('Content-Type', 'application/json');
    
        return $response; 
    }
        /**
     * @Route("/profile/adress/add/ajax", name="user.adressadd.ajax")
     */
    public function addAdressAjax(Request $request)
    {
        if(!isset($_SESSION))
        {
            session_start();
        }

        $user = $this->getUser();

        $adress = new Adress;

        $adress->setFirstName($request->request->get('firstName'))
                ->setLastName($request->request->get('lastName'))
                ->setPhoneNumber($request->request->get('phoneNumber'))
                ->setAdress($request->request->get('adress'))
                ->setCodePostal($request->request->get('codePostal'))
                ->setCity($request->request->get('city'))
                ->setCountry($request->request->get('country'))
                ->setType(1);

        $user->addAdress($adress);
        $this->em->flush();

        $adress = $user->getAdress();

        $response = new Response(json_encode(array(
            'idAdress' => ($adress[count($adress)-1])->getId()
        )));

        $response->headers->set('Content-Type', 'application/json');
    
        return $response; 
    }
}
