<?php

namespace App\Controller;


use App\Entity\Livreur;
use App\Entity\Profile;
use App\Form\ProfileType;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProfilesController
 * @package App\Controller
 * @Route("/content")
 */

class ProfilesController extends AbstractController
{
    public function logExiste($login){

        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->get('https://boulang.ml/profils/Login/'.$login);

            $status =$response->getStatusCode();

            return true;
        }catch (\GuzzleHttp\Exception\RequestException $e)
        {
            return false;
        }
    }

    public function putProfile($profileJson, $matricule){

        $client = new \GuzzleHttp\Client();

        try{
            $response = $client->request('PUT', 'https://boulang.ml/profils/'. $matricule,
                [
                    'body' => $profileJson
                ]);

            return $response->getStatusCode();
        }catch (\GuzzleHttp\Exception\RequestException $e){

            return 500;
        }

    }

    public function postLivreur($LivreurJson){

        $client = new \GuzzleHttp\Client();

        try{
            $response = $client->request('POST', 'https://boulang.ml/livreurs', [
                'body' => $LivreurJson
            ]);

            $status =$response->getStatusCode();
            return $status;
        }catch(\GuzzleHttp\Exception\RequestException $e){ return 500; }

    }

    public function getCredentials($matricule){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://boulang.ml/profils/'.$matricule);

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $profile = (array)(json_decode($response));



        return $profile;

    }



    public function postProfile($ProfileJson){

        $client = new \GuzzleHttp\Client();

        try{
            $response = $client->request('POST', 'https://boulang.ml/profils', [
                'body' => $ProfileJson
            ]);

            $status =$response->getStatusCode();
            return $status;
        }catch (\GuzzleHttp\Exception\RequestException $e){
            return 500;
        }

    }

    public function deletefun($matricule){

        $client = new \GuzzleHttp\Client();
        try{
            $response = $client->delete('https://boulang.ml/profils/'.$matricule);
            $status = $response->getStatusCode() ;
            return $status;
         }catch(\GuzzleHttp\Exception\RequestException $e){
            return 500;
        }

        }

    public function getProfiles(){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://boulang.ml/profils');

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $Profils = (array)(json_decode($response));

        $newProfiles = array();

        foreach($Profils as $Profil)
        {
            $Profil = (array) $Profil;
            $newProfiles[] = $Profil;
        }

        return $newProfiles;
    }

    /**
     * @Route("/Profiles", name="profiles")
     */
    public function profiles()
    {

     $profils =  $this->getProfiles();

        return $this->render('content/profiles.html.twig', [
            'profils'=> $profils
        ]);
    }

    /**
     * @Route("/Profiles/delete/{matricule}", name="profile_delete")
     */

    public function delete(Request $request, $matricule) : Response{

       $status = $this->deletefun($matricule);

       if($status>=200 && $status<300)
            return $this->json(['code' => 200 , 'message' => 'Profil supprimé avec succes'] , 200);

       else
            return $this->json(['code' => $status , 'message' => 'oops'] , $status);


    }

    /*lost case*/
   /*delete a profile from profiles and boom u still can get it by id */
    /**
     * @Route("/Profiles/modify/{matricule}" , name="profile_modify")
     */

    public function modify(Request $request, $matricule) : Response{

        $Profile = new Profile();

        $Cred = $this->getCredentials($matricule);

        $login = $Cred['login'];

        $form = $this->createForm(ProfileType::class,$Profile);

        $form->handleRequest($request);
        if ($form->isSubmitted() ){

            $Profile = $Profile->toArray();
            array_shift($Profile);
            array_pop($Profile);
            if($this->logExiste($Profile['login']))
                $Profile['login'] = $login;

            $serializer = $this->container->get('serializer');
            $Profile = $serializer->serialize($Profile, 'json');

            $status = $this->putProfile($Profile, $matricule);

           return $this->redirectToRoute('profiles');


        }


        return $this->render('modals/profileModifyModal.html.twig',[
            'form_modify' => $form->createView(), 'matricule'=> $matricule ,'credentials' => $Cred ]);

    }

    /**
     * @Route("/Profiles/add" , name="profile_add")
     */

    public function add(Request $request){

        $Profile = new Profile();
   $isLivreur = false;

        $form= $this->createForm(ProfileType::class, $Profile);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){

            $Livreur = new Livreur();
            $Profile = $Profile->toArray();
            array_shift($Profile);
            array_pop($Profile);
            $serializer = $this->container->get('serializer');
            if($Profile['affectation'] == 'Livreur')
            {
                $isLivreur = true;

                $Livreur->setMatricule($Profile['matricule']);
                $Livreur->setTeleLivreur(1);
                $Livreur->setNumVehicule('0000');
                $Livreur = $Livreur->toArray();
                array_shift($Livreur);
                $Livreur = $serializer->serialize($Livreur, 'json');
            }

            $Profile = $serializer->serialize($Profile, 'json');

             $status =  $this->postProfile($Profile);
            if($isLivreur)
            {
                $status = $this->postLivreur($Livreur);
            }

             if($status>= 200 && $status<300)
             {
                 echo '<script language="javascript">';
                 echo 'alert("Profil ajouté avec succes")';
                 echo '</script>';
             }
             else{
                 echo '<script language="javascript">';
                 echo 'alert("Oops! une erreur s\'est produite, essayez plus tard")';
                 echo '</script>';
             }

             $Profile = new Profile();
             $form= $this->createForm(ProfileType::class, $Profile);
             $this->redirectToRoute($request->attributes->get('_route'));

        }

        return $this->render("content/addProfile.html.twig", ['form_profile' => $form->createView()]);
    }
}
