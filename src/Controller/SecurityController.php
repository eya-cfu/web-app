<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /*the authentification may be decapricated for instance, however clicking on the login button will take you*/
    /*straight to the content page*/

    /**
     * @Route("/", name="security")
     */
    public function index()
    {
        return $this->render('Authentification/login.html.twig');
    }
}
