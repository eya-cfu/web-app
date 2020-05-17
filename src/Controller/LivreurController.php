<?php

namespace App\Controller;

use App\Entity\Livreur;
use App\Form\LivreurType;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class LivreurController extends AbstractController
{

    public function getLivreurByMatricule($matricule){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/livreurs/'.$matricule);

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();
        $Livreur = (array)(json_decode($response));

        return $Livreur;

    }


    public function putLivreur($LivreurJson , $matricule){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('PUT', 'https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/livreurs/'. $matricule,
            [
                'body' => $LivreurJson
            ]);

        $status =$response->getStatusCode();
        return $status;
    }


    public function postLivreur($LivreurJson){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/livreurs', [
            'body' => $LivreurJson
        ]);

        $status =$response->getStatusCode();
        return $status;
    }
    public function getLivreurs(){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/livreurs');

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
     * @Route("/Livreurs/add" , name="livreur_add")
     */

    public function add(Request $request){

        $Livreur = new Livreur();


        $form= $this->createForm(LivreurType::class, $Livreur);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){


            $Livreur= $Livreur->toArray();
          array_shift($Livreur);
            $serializer = $this->container->get('serializer');
            $Livreur = $serializer->serialize($Livreur, 'json');
             dump($Livreur);
            $status =  $this->postLivreur($Livreur);

            if($status == 201)
            {
                echo '<script language="javascript">';
                echo 'alert("Livreur ajouté avec succes")';
                echo '</script>';
            }
            else{
                echo '<script language="javascript">';
                echo 'alert("Oops! une erreur s\'est produite, réessayez plus tard")';
                echo '</script>';
            }


            $Livreur = new Livreur();
            $form= $this->createForm(LivreurType::class, $Livreur);
            $this->redirectToRoute($request->attributes->get('_route'));

        }

        return $this->render("content/addLivreur.html.twig", ['form_Livreur' => $form->createView()]);
    }

    /**
     * @Route("/Livreurs/delete/{matricule}", name="livreur_delete")
     */

    public function delete(Request $request, $matricule) : Response{
        $client = new \GuzzleHttp\Client();
        $response = $client->delete('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/livreurs/'.$matricule);
        $responsePro = $client->delete('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils/'.$matricule);

        if($response->getStatusCode() == 200  &&  $responsePro->getStatusCode() == 200);
        return $this->json(['code' => 200 , 'message' => 'Livreur supprimé avec succes'] , 200);


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
