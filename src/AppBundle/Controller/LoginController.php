<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(){
        return $this->render(':Otel:login.html.twig');
    }

    /**
     * @Route("/signup", name="signup")
     */
    public function signupAction(){
        return $this->render(':Otel:sign-up.html.twig');
    }
}
