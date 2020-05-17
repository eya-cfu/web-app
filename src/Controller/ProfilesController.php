<?php

namespace App\Controller;


use App\Entity\Profile;
use App\Form\ProfileType;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ProfilesController extends AbstractController
{

    public function getCredentials($matricule){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils/'.$matricule);

        $status =$response->getStatusCode();

        $response = $response->getBody()->getContents();

        $profile = (array)(json_decode($response));



        return $profile;

    }



    public function postProfile($ProfileJson){

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', 'https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils', [
            'body' => $ProfileJson
        ]);

        $status =$response->getStatusCode();
        return $status;
    }

    public function getProfiles(){

        $client = new \GuzzleHttp\Client();


        $response = $client->get('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils');

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
        $client = new \GuzzleHttp\Client();
        $response = $client->delete('https://virtserver.swaggerhub.com/Boulangerie/ApiCourse/1.0.0/profils/'.$matricule);

        if($response->getStatusCode() == 200);
        return $this->json(['code' => 200 , 'message' => 'Profil supprimé avec succes'] , 200);


    }

    /**
     * @Route("/Profiles/modify/{matricule}" , name="profile_modify")
     */

    public function modify(Request $request, $matricule) : Response{

        $Profile = new Profile();

        $Cred = $this->getCredentials($matricule);

        $form = $this->createForm(ProfileType::class,$Profile);

        $form->handleRequest($request);
        if ($form->isSubmitted() ){

            $Profile = $Profile->toArray();
            array_shift($Profile);
            array_pop($Profile);
            $serializer = $this->container->get('serializer');
            $Profile = $serializer->serialize($Profile, 'json');

            $status = $this->postProfile($Profile);

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


        $form= $this->createForm(ProfileType::class, $Profile);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){

            $Profile = $Profile->toArray();
            array_shift($Profile);
            array_pop($Profile);
            $serializer = $this->container->get('serializer');
            $Profile = $serializer->serialize($Profile, 'json');

            dump($Profile);

             $status =  $this->postProfile($Profile);

             if($status == 201)
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
