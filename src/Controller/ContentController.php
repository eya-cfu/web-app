<?php

namespace App\Controller;


require __DIR__ . '\..\..\vendor\autoload.php' ;

use App\Entity\Boulangerie;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
class ContentController extends AbstractController
{
    public function getBoulNameById($id) {
        $client = new \GuzzleHttp\Client();
        $response = $client->get("https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/boulangeries/ById/".$id);

        $status =$response->getStatusCode();
        $response = $response->getBody()->getContents();
        $Object=json_decode($response);

        return $Object->nomBL;
    }



    /**
     * @Route("/", name="content")
     */
    public function index()
    {
        return $this->render('content/acceuil.html.twig');
    }



    /**
     * @Route("/Historique" , name="historique")
     */

    public function hist(){

        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/commandesBL/getCmdsByEtat?etat="honoree"');

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();
        $commande= (array)(json_decode($response));
        $commande['nomBL'] =  $this->getBoulNameById( $commande['idCommandeBL']);

        $commandes[] = $commande;

        return $this->render('content/Historique.html.twig', ['commandes'=> $commandes]);

    }

    /**
     * @Route("/historique/{id}/{nomBL}/{dueDate}", name="details_commande")
     */

    public function detail($id, $nomBL, $dueDate){

        $client = new \GuzzleHttp\Client();



        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/commandesBL/'.$id.'/detailsCmdBL');

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();
        $produits = (array)(json_decode($response));
        $details = array();

        foreach($produits as $produit)
        {
            $produit = (array) $produit;
            $details[] = $produit;

        }


        return $this->render('content/DetailsCommande.html.twig', ['details' => $details, 'id' => $id, 'nomBL'=> $nomBL , 'dueDate'=> $dueDate]);

    }
}
