<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Bill;
use App\Entity\CommandLine;
use App\Entity\Payment;
use App\Repository\AdressRepository;
use App\Repository\BasketRepository;
use App\Repository\BillRepository;
use App\Repository\BlueCardRepository;
use App\Repository\CommandLineRepository;
use App\Repository\GlobalParameterRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use nusoap_client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BasketController extends AbstractController
{
    private $em;
    private $basketRepository;
    private $commandLineRepository;
    private $productRepository;
    private $globalParameterRepository;
    private $adressRepository;
    private $billRepository;
    private $blueCardRepository;

    public function __construct(BlueCardRepository $blueCardRepository,BillRepository $billRepository,AdressRepository $adressRepository ,GlobalParameterRepository $globalParameterRepository, ProductRepository $productRepository, CommandLineRepository $commandLineRepository,EntityManagerInterface $em,BasketRepository $basketRepository)
    {
        $this->em = $em;
        $this->basketRepository = $basketRepository;
        $this->commandLineRepository = $commandLineRepository;
        $this->productRepository = $productRepository;
        $this->globalParameterRepository = $globalParameterRepository;
        $this->adressRepository = $adressRepository;
        $this->billRepository = $billRepository;
        $this->blueCardRepository = $blueCardRepository;
    }
        /**
     * @Route("/monpanier", name="basket.show")
     */
    public function showBasket()
    {
        if(!isset($_SESSION))
        {
            session_start();
        }
        if(empty($_SESSION['idBasket']))
        {
            return $this->render('basket/index.html.twig',[

            ]);
        }
        else
        {
            $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);

            $this->basketRepository->calculPriceShip($basket);

            $this->basketRepository->calculPrice($basket);

            return $this->render('basket/index.html.twig',
                                ['basket' => $basket
                                ]);
        }
    }

    /**
     * @Route("/updatebasket", name="basket.update")
     */
    public function updateBasket()
    {
        if($this->getUser() && $this->getUser()->getRole() == 1)
        {
            return $this->redirectToRoute('homepage');
        }

        $basket = new Basket;
        if(empty($_SESSION['idBasket']))
        {
            $basket->setTimeStamp(null);
            $user = $this->getUser();
            if (empty($user))
            {
                $basket->setTimeStamp(new \DateTime('@'.strtotime('now')));
            }
            else{
                $basket->setUser($user);
            }
            $basket->setState(0)
            ->setShippingCost(0)
            ->setMethodShipp(0)
            ->setdateCreated(new \DateTime('@'.strtotime('now')));

            $this->em->persist($basket);
            $this->em->flush();

            $basket = $this->basketRepository->findLast();
            $this->get('session')->set('idBasket', $basket->getId());
            $_SESSION['idBasket'] = $basket->getId(); 
        }        
        else{
            $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);
        }
        //$_POST['idProduct']
        $product = $this->productRepository->findOneById($_POST['idProduct']);

        $commandLine = $this->commandLineRepository
                            ->findOneBy(array('basket'=>$basket,'product'=>$product)); 
    
        if(empty($commandLine))
        {  
            $commandLine = New CommandLine;
            $commandLine
                ->setProduct($product)
                ->setQuantity($_POST['quantity']);

            $basket->addCommandLine($commandLine);
        }
        else
        {
            $commandLine
                ->setQuantity(
                    $commandLine->getQuantity('quantity')+$_POST['quantity']
                );
        }

        $this->em->flush();

        $this->basketRepository->calculPrice($basket);

        return $this->redirectToRoute('homepage');
    }
    /**
     * @Route("/linkbasket", name="basket.link")
     */
    public function linkBasket()
    {
        $productBasket = 0;

        if(isset($_SESSION['idBasket']))
        {
            $basket = $this->basketRepository
            ->findOneById($_SESSION['idBasket']);

            if($basket != null)
            {
                $commandLines = $basket->getCommandLines();

                foreach ($commandLines as $value) 
                {
                    $productBasket += $value->getQuantity();
                }
                
            }
            else
            {
                unset($_SESSION['idBasket']);
            }
        }
        return $this->render('partial/basket/_linkbasket.html.twig',['productBasket'=> $productBasket]);
    }
            /**
     * @Route("/monpanier/information", name="basket.information")
     */
    public function informationBasket()
    {
        if(!isset($_SESSION))
        {
            session_start();
        }

        $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);
        $basket->setMethodShipp($_POST['shippingMode']);

        $basket = $this->basketRepository->calculPriceShip($basket);

        $user = $this->getUser();

        return $this->render('basket/information.html.twig',
        ['user' => $user,
        'methodShipp' => $basket->getMethodShipp()
        ]);
    }
    /**
     * @Route("/monpanier/envoi", name="basket.send")
     */
    public function sendBasket()
    {
        if(!isset($_SESSION))
        {
            session_start();
        }
        
        if(isset($_SESSION['idBasket']))
        {
            $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);

            $user = $basket->getUser();

            if (empty($user))
            {
                $basket->setState(1);
                $this->em->flush();
                return $this->redirectToRoute('app_login');
            }

            $adress = $this->adressRepository->findOneById($_POST['idAdress']);
            $basket->setAdress($adress);
            $this->em->flush();
            
            if ($basket->getMethodShipp() == 0)
            {
                // Global Settings definition
                // Définition des paramètres globaux
                $MR_WebSiteId = "BDTEST13";
                $MR_WebSiteKey = "PrivateK";
                $client = new nusoap_client("http://api.mondialrelay.com/Web_Services.asmx?WSDL", true);
                $client->soap_defencoding = 'utf-8';
                
                // We define the parameters as a string array. Each Key/Val represents a parameter of the soap call
                // On défini les paramètres dans un tableau de chaînes. Chaque paire Clé/Valeur est un paramètre del'appel SOAP
                $params = array(
                'Enseigne' => $MR_WebSiteId,
                'Pays' => "FR",
                /*'NumPointRelais' => "",*/
                'Ville' => $adress->getCity(),
                'CP' => $adress->getCodePostal(),
                'Latitude' => "",
                'Longitude' => "",
                'Taille' => "",
                'Poids' => "",
                'Action' => "",
                'DelaiEnvoi' => "0",
                'RayonRecherche' => "20",
                //'TypeActivite' => "",
                //'NACE' => "",
                'NombreResultats' => "10",
                );
                // We generate the request's security code
                // On génère la clé de sécurité de l'appel
                $code = implode("", $params);
                $code .= $MR_WebSiteKey;
                $params["Security"] = strtoupper(md5($code));
                // We make the call and load it in the $result var
                // On réalise l'appel et stocke le résultat dans la variable $result
                $result = $client->call(
                'WSI4_PointRelais_Recherche',
                $params,
                'http://api.mondialrelay.com/',
                'http://api.mondialrelay.com/WSI4_PointRelais_Recherche'
                );
    
                // We check their is no error during the process
                // On vérifie qu'il n'y a pas eu d'erreur
                if ($client->fault)
                {
                    echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>';
                    print_r($result);
                    echo '</pre>';
                }
    
                $this->em->flush();
    
                return $this->render('basket/mondialrelay.html.twig',[
                    'content'=> $result]);
            }
            else{
                return $this->redirectToRoute('basket.validate');
            }
        }
        else{
            return $this->redirectToRoute('homepage');
        }
        
    }
    /**
     * @Route("/monpanier/validation", name="basket.validate")
     */
    public function validateBasket()
    {
        if(!isset($_SESSION))
        {
            session_start();
        }

        $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);

        if(!empty($_POST))
        {
            $basket->setIdRelay($_POST['idPointRelai']);
        }
        $basket->setState(2);
        //MAJ du prix du panier
        $basket = $this->basketRepository->calculPrice($basket);
        //MAJ du prix d'envoi du panier
        $basket = $this->basketRepository->calculPriceShip($basket);

        return $this->render('basket/validate.html.twig',[
            'basket'=> $basket]);
    
    }

    /**
     * @Route("/paiement", name="basket.pay")
     */
    public function payBasket()
    {
        if(!isset($_SESSION))
        {
            session_start();
        }
        
        if(isset($_SESSION['idBasket']))
        {
            $basket = $this->basketRepository->findOneById($_SESSION);
            $basketState = $basket->getState();

            if ($basketState != 2)
            {
                return $this->redirectToRoute('homepage');
            }

            //Vérification des Stocks
            $error = $this->basketRepository->validateStockBasket($basket);
            
            if(!empty($error))
            {
                if(isset($_SESSION['idBasket']))
                {
                    $basket->setState(1);
                    $this->em->flush();
                    return $this->render('basket/index.html.twig',
                                        ['basket' => $basket,
                                        'error' => $error
                                        ]);
                }
                else
                {
                    return $this->render('basket/index.html.twig',['error' => $error]);
                }
            }
            else
            {
                $payment = new Payment;
                $payment->setBasket($basket)
                        ->setUser($basket->getUser())
                        ->setValue(
                            $basket->getPrice() + 
                            $basket->getShippingCost())
                        ->setTransactionId('0')
                        ->setType(1);

                //Si le paiement a fonctionné
                foreach ($basket->getCommandLines() as $commandLine) {
                    $product = $commandLine->getProduct();
                    $product->setQuantity(
                        $product->getQuantity() - $commandLine->getQuantity()
                    );
                }

                $basket->setState(3);
                $this->em->persist($payment);
                $this->em->flush();
                
                return $this->redirectToRoute('bill.generate');
            }
        }
        else{
            return $this->redirectToRoute('homepage');
        }
    }
        /**
     * @Route("/panier_valide", name="basket.end")
     */
    public function endBasket()
    {
        session_start();
        if(isset($_SESSION['idBasket']))
        {
            $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);
            $bill = $this->billRepository->findOneByUser($basket->getUser(),['id' => 'DESC']);

            if ($basket->getState() != 3)
            {
                return $this->redirectToRoute('homepage');
            }
     
            unset($_SESSION['idBasket']);
            
            return $this->render('basket/end.html.twig',
            ['bill' => $bill->getId()
            ]); 
        }
        else{
            return $this->redirectToRoute('homepage');
        }
    }
    /**
     * @Route("/panier/ajax/shippingmode", name="basket.shippingMode")
     */
    public function ajaxShippingMode(Request $request)
    {
        $shippingMode = $request->request->get('shippingMode');        

        if(!isset($_SESSION))
        {
            session_start();
        }

        $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);

        $basket->setMethodShipp($shippingMode);

        $basket = $this->basketRepository->calculPriceShip($basket);

        $response = new Response(json_encode(array(
            'basketPrice' => $basket->getPrice(),
            'shippingPrice' => $basket->getShippingCost(),
        )));

        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
}
