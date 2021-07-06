<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CsvController extends AbstractController
{
    /**
     * @Route("/csv", name="csv")
     */
    public function index()
    {
        $list = array(
            //these are the columns
            ['Nom','Prénom'],
            //these are the rows
            ['Andrei', 'Boar'],
            ['John', 'Doe']
        );

        $fp = fopen('php://temp', 'w');
        foreach ($list as $fields) {
            fputcsv($fp, $fields);
        }
        
        rewind($fp);
        $response = new Response(stream_get_contents($fp));
        fclose($fp);
        
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="testing.csv"');
        
        return $response;
    }
}
