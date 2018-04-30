<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ContactController extends Controller
{
    /**
     * @Route("/contact-us",name="contact")
     */
    public function IndexAction(){
        return $this->render(':Otel:contact.html.twig');
    }
}
