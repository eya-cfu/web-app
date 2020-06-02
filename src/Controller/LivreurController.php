<?php

namespace App\Controller;

use App\Entity\Livreur;
use App\Form\LivreurType;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LivreurController
 * @package App\Controller
 * @Route("/content")
 */

class LivreurController extends AbstractController
{

    public function getLivreurByMatricule($matricule){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://boulang.ml/livreurs/'.$matricule);

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();
        $Livreur = (array)(json_decode($response));

        return $Livreur;

    }


    public function putLivreur($LivreurJson , $matricule){

        $client = new \GuzzleHttp\Client();

       try{
           $response = $client->request('PUT', 'https://boulang.ml/livreurs/'. $matricule,
               [
                   'body' => $LivreurJson
               ]);

           $status =$response->getStatusCode();
           return $status;
       }catch (\GuzzleHttp\Exception\RequestException $e){
           return 500;
       }

    }


    public function getLivreurs(){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://boulang.ml/livreurs');

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $Livreurs = (array)(json_decode($response));

        $finaLivreurs = array();

        foreach($Livreurs as $Livreur)
        {
            $Livreur = (array) $Livreur;
            $nom = $Livreur ['nom'];
            $Livreur = (array)$Livreur['livreur'];
            $Livreur['nomLivreur'] = $nom;
            $finaLivreurs[] = $Livreur;
        }


        return $finaLivreurs;
    }
    /**
     * @Route("/Livreurs", name="livreurs")
     */
    public function Livreurs()
    {

        $Livreurs =  $this->getLivreurs();

        return $this->render('content/Livreurs.html.twig', [
            'Livreurs'=> $Livreurs
        ]);
    }


    /**
     * @Route("/Livreurs/delete/{matricule}", name="livreur_delete")
     */

    public function delete(Request $request, $matricule) : Response{
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->delete('https://boulang.ml/livreurs/' . $matricule);
            return $this->json(['code' => 200 , 'message' => 'Livreur supprimé avec succes'] , 200);

        }catch(\GuzzleHttp\Exception\RequestException $e){
            return $this->json(['code' => 200 , 'message' => 'requete echouée :('] , 500);

        }

    }

    /**
     * @Route("/Livreurs/modify/{matricule}" , name="livreur_modify")
     */

    public function modify(Request $request, $matricule) : Response{

        $Livreur = new Livreur();

        $Mod = $this->getLivreurByMatricule($matricule);

        $form = $this->createForm(LivreurType::class,$Livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted()){

           $Livreur = $Livreur->toArray();
           array_shift($Livreur);
            $serializer = $this->container->get('serializer');
            $Livreur = $serializer->serialize($Livreur, 'json');

            dump($Livreur);

            $status = $this->putLivreur($Livreur,$matricule);


            return $this->redirectToRoute('livreurs');

            // return $this->json(['code'=> 200 , 'message'=> 'boulangerie modified','Livreur'=> $Livreur], 200);
        }


        return $this->render('modals/LivreurModifyModal.html.twig',[
            'form_modify' => $form->createView(),
            'matricule'=> $matricule ,
            'Mod'=> $Mod]);

    }

}
