<?php

namespace App\Controller;

use App\Entity\Boulangerie;
use GuzzleHttp\Client;
use App\Form\BoulangerieType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BoulangerieController
 * @package App\Controller
 * @Route("/content")
 */

class BoulangerieController extends AbstractController
{

    public function  matExiste($matricule){
        $client = new \GuzzleHttp\Client();

        try{
            $response = $client->get('https://boulang.ml/profils/'.$matricule);
            return true;
        }catch(\GuzzleHttp\Exception\ClientException $e){

            return false;
        }
    }


    public function putBoulangerie($BoulJson , $id){

        $client = new \GuzzleHttp\Client();

        try{
            $response = $client->request('PUT', 'https://boulang.ml/boulangeries/ById/'. $id,
                [
                    'body' => $BoulJson
                ]);

           return $response->getStatusCode();
        }catch (\GuzzleHttp\Exception\RequestException $e){

            return 500;
        }


    }

    public function postBoulangerie($BoulJson){

        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->request('POST', 'https://boulang.ml/boulangeries', [
                'body' => $BoulJson
            ]);

            $status =$response->getStatusCode();
            return $status;
        }catch(\GuzzleHttp\Exception\RequestException $e){
            return 500;
        }

    }

    public function getBoulangerieById($id){
        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://boulang.ml/boulangeries/ById/'.$id);

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();
        $Boulangerie = (array)(json_decode($response));

        return $Boulangerie;
    }

    public function getBoulangeries(){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://boulang.ml/boulangeries');

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $Boulangeries= (array)(json_decode($response));

        $BoulangeriesSendable = array();

        foreach($Boulangeries as $Boulangerie)
        {
            $Boulangerie = (array) $Boulangerie;
            $responsable = (array) $Boulangerie['nom'];
            $Boulangerie = (array) $Boulangerie['Boulangerie'];
            $Boulangerie['nomResponsable'] = $responsable[0];
            $BoulangeriesSendable [] = $Boulangerie;
        }


        return $BoulangeriesSendable;
    }

    /**
     * @Route("/Boulangeries", name="boulangeries")
     */

    public function boulangerie(Request $request)
    {

        $Boulangeries = $this->getBoulangeries();

            return $this->render('content/Boulangeries.html.twig',[
                'Boulangeries' => $Boulangeries
            ]);
    }

    /**
     * @Route("/Boulangeries/delete/{id}", name="boulangerie_delete")
     */

    public function delete(Request $request, $id) : Response{
        $client = new \GuzzleHttp\Client();
        $response = $client->delete('https://boulang.ml/boulangeries/ById/'.$id);

        if(($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300))
        return $this->json(['code' => 200 , 'message' => 'Boulangerie supprimée avec succes'] , 200);
        else
        return $this->json(['code' => $response->getStatusCode() , 'message' => 'oops'] , 500);


    }

    /**
     * @Route("/Boulangeries/modify/{id}" , name="boulangerie_modify")
     */

    public function modify(Request $request, $id) : Response{

         $Boulangerie = new Boulangerie();

         $Mod = $this->getBoulangerieById($id);
         $oldMatricule= $Mod['matricule'];
         $form = $this->createForm(BoulangerieType::class,$Boulangerie);
        $form->handleRequest($request);

        if ($form->isSubmitted()){




            $Boulangerie= $Boulangerie->toArray();
            array_shift($Boulangerie);
            $Boulangerie = array('id_Boulangerie' => $id) + $Boulangerie;


            if (!($this->matExiste($Boulangerie['matricule'])))
                $Boulangerie['matricule'] = $oldMatricule;


            $serializer = $this->container->get('serializer');
            $Boulangerie = $serializer->serialize($Boulangerie, 'json');


            dump($Boulangerie);

            $status = $this->putBoulangerie($Boulangerie,$id);


             return $this->redirectToRoute('boulangeries');



            }


        return $this->render('modals/BoulModifyModal.html.twig',[
            'form_modify' => $form->createView(),
            'id'=> $id,
            'Mod'=> $Mod ]);

    }

    /*add Boul not done yet*/

    /**
     * @Route("/Boulangeries/add" , name="boulangerie_add")
     */

    public function add(Request $request){

        $boulangerie = new Boulangerie();


        $form= $this->createForm(BoulangerieType::class, $boulangerie);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){

            $boulangerie= $boulangerie->toArray();
            array_shift($boulangerie);
            $boulangerie['idBoulangerie'] = null;


            $serializer = $this->container->get('serializer');
            $boulangerie = $serializer->serialize($boulangerie, 'json');
            dump($boulangerie);
            $status =  $this->postBoulangerie($boulangerie);

            if($status >= 200 && $status<300)
            {
                echo '<script language="javascript">';
                echo 'alert("Boulangerie ajoutée avec succes")';
                echo '</script>';
            }
            else{
                echo '<script language="javascript">';
                echo 'alert("Oops! une erreur s\'est produite, essayez plus tard")';
                echo '</script>';
            }

            $boulangerie = new Boulangerie();
            $form= $this->createForm(BoulangerieType::class, $boulangerie);
            $this->redirectToRoute($request->attributes->get('_route'));

        }

        return $this->render("content/addBoulangerie.html.twig", ['form_boul' => $form->createView()]);
    }
}
