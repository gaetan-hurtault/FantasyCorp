<?php

namespace App\Controller;

use App\Entity\Bill;
use App\Repository\BasketRepository;
use App\Repository\BillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BillController extends AbstractController
{
    private $basketRepository;
    private $billRepository;
    private $em;

    public function __construct(BillRepository $billRepository, BasketRepository $basketRepository, EntityManagerInterface $em)
    {
        $this->basketRepository = $basketRepository;
        $this->billRepository = $billRepository;
        $this->em = $em;
    }

    /**
     * @Route("/bill", name="bill.generate")
     */
    public function generateBill()
    {
        session_start();
        if(isset($_SESSION['idBasket']))
        {
            $basket = $this->basketRepository->findOneById($_SESSION['idBasket']);

            if ($basket->getState() != 3)
            {
                return $this->redirectToRoute('homepage');
            }
            $billName = 'Facture_n°'.$basket->getId();
            // Configure Dompdf according to your needs
            $pdfOptions = new Options();
            $pdfOptions->set('defaultFont', 'Arial');
            
            // Instantiate Dompdf with our options
            $dompdf = new Dompdf($pdfOptions);

            // Retrieve the HTML generated in our twig file
            $html = $this->renderView('pdf/bill.html.twig', [
                'title' => "Facture n°".$basket->getId(),
                'basket' => $basket
            ]);
            
            // Load HTML to Dompdf
            $dompdf->loadHtml($html);
            
            // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
            $dompdf->setPaper('A4', 'portrait');

            // Render the HTML as PDF
            $dompdf->render();

            // Store PDF Binary Data
            $output = $dompdf->output();
            
            // In this case, we want to write the file in the public directory
            $publicDirectory = $this->getParameter('bill_directory');
            // e.g /var/www/project/public/mypdf.pdf
            $pdfFilepath =  $publicDirectory . '/'.$billName.'.pdf';
            
            // Write file to the desired path
            if (file_put_contents($pdfFilepath, $output))
            {              
            // Send some text response
            //return new Response("The PDF file has been succesfully generated !");
            
            //création de la facture en BDD et maj du panier
            $bill = new Bill;

            $bill->setDateCreated(new \DateTime('@'.strtotime('now')));
            $bill->setUser($basket->getUser());
            $bill->setBasket($basket);
            $bill->setName($billName);
            $this->em->persist($bill);
            $this->em->flush();
            
            $basket->setState(3);
            $this->em->flush();

            return $this->redirectToRoute('basket.end');
            }
            else
            {
                //gestion de l'erreur de création
            }
        }
        else{
            return $this->redirectToRoute('homepage');
        }
    }
    /**
     * @Route("/profile/ma_facture_{idBill}", name="bill.show")
     */
    public function showBill(Bill $idBill)
    {
        $filename = $this->billRepository->findOneBy(['user'=>$this->getUser(),'id'=>$idBill->getId()]);

        if (empty($filename))
        {
            return $this->redirectToRoute('homepage');
        }

        return new BinaryFileResponse($this->getParameter('bill_directory').'/'.$filename->getName().'.pdf');
    }
}
