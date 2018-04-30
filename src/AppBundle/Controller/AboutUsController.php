<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AboutUsController extends Controller
{
    /**
     * @Route("/about-us",name="about-us")
     */
    public function IndexAction(){
        return $this->render(':Otel:about-us.html.twig');
    }
}
