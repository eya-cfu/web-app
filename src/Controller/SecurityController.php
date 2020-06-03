<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /*the authentification may be decapricated for instance, however clicking on the login button will take you*/
    /*straight to the content page*/

    /**
     * @Route("/", name="security")
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {
        if($this->getUser())
        {
           return $this->redirectToRoute('content');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('Authentification/login.html.twig', [
            'error'=> $error
        ]);
    }

    /**
     * @Route("/deconnexion" , name="deconnexion")
     */

    public function deconnecter(){

    }
}
